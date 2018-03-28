<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CenterRequest;

use App\Center;
use App\ContactInfo;
use App\Address;
use App\Area;
use App\City;
use App\Services\Centers;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CentersController extends Controller
{
    
    public function __construct(Centers $centers)
    {
        $this->centers=$centers;
     
    }

    function canEdit()
    {
        return $this->currentUserIsDev();
    }
    function canImport()
    {
        return $this->currentUserIsDev();
    }
   
    function areaOptions()
    {
        $areas=Area::all();

        $options = $areas->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        array_unshift($options, ['text' => '----------' , 'value' =>'']);
       

        return $options;
    }

    function cityOptions()
    {
        $cities=City::with(['districts'])->get(); 
        return $cities;
    }

    public function seedDiscountCenters()
    {
       
        if(!$this->currentUserIsDev()) return;
        $eastCenter = Center::where('removed',false)->where('head',true)->first();
       
        $eastDiscountCodes=[
            "new" , "multi" , "member" , "lotus", "over65", "poor", "religion"
        ];
        foreach($eastDiscountCodes as $eastDiscountCode){
           
            $discount=\App\Discount::where('code' , $eastDiscountCode)->first();
           
            $eastCenter->discounts()->attach($discount->id);
        }


        $westDiscountCodes = [
            "one-west", "multi-west", "over65-west" ,"helf-west" ,"lotus-west"
        ];


        $westCenters =Center::where('removed',false)->where('head',false)->where('oversea',false)->get(); 
        foreach ($westCenters as  $westCenter)
        {
            foreach($westDiscountCodes as $westDiscountCode){
                $discount=\App\Discount::where('code' , $westDiscountCode)->first();
                $westCenter->discounts()->attach($discount->id);
            }
        }
    }
    
    public function index()
    {
       
        $request=request();

        $oversea=false;
        if($request->oversea)  $oversea=Helper::isTrue($request->oversea);

        $area=0;
        if($request->area)  $area=(int)$request->area;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);

        if(!$active) $centers=$this->centers->getAll()->where('active',$active);
        else $centers=$this->centers->fetchCenters($oversea,$area,$active);
        
       
        $centers=$this->centers->getOrdered($centers);
       
      
        $pageList = new PagedList($centers);

        foreach($pageList->viewList as $center){
            $center->loadContactInfo();
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');
       
        return view('centers.index')->with([
            'title' => '開課中心管理',
            'menus' => $menus,
            'areas' => $this->areaOptions(),
            'canEdit' => $this->canEdit(),
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $areaOptions=$this->areaOptions();
        $cityOptions=$this->cityOptions();
        $center=Center::init();
       

        $center['contactInfo']['address']['cityId']=$cityOptions->first()->id;
      
        $form=[
            'center' => $center,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
        ];

        return response() ->json($form);
      
    }

    public function store(CenterRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $center=new Center($request->getCenterValues());
        $contactInfo=new ContactInfo($request->getContactInfoValues());
        $address=new Address($request->getAddressValues());
        
        $updatedBy=$current_user->id;
        $center->updatedBy=$updatedBy;
        $contactInfo->updatedBy=$updatedBy;
        $address->updatedBy=$updatedBy;

        $center = $this->centers->createCenter($center,$contactInfo,$address);
        return response() ->json($center);
    }

    public function show($id)
    {
        $center = $this->centers->getById($id);
        if(!$center) abort(404);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $center->loadContactInfo();
        $center->canEdit=$this->canEdit();

        if($center->contactInfo)  $center->contactInfo->canEdit= $center->canEdit;
       

        return response() ->json($center);
        
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $center = Center::findOrFail($id);

        $areaOptions= $this->areaOptions();
        $cityOptions=$this->cityOptions();
      
        $form=[
            'center' => $center,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
        ];

        return response() ->json($form);
       
        
    }


    public function update(CenterRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $center = Center::findOrFail($id);
       
        $values=$request->getCenterValues();

        $values['updatedBy'] = $current_user->id;
        
        $center->update($values);
        return response() ->json();
    }

    public function importances(Request $request)
    {
        $values=$request->get('importances');
        foreach ($values as $value)
        {
            $value['id'];
            $value['importance'];
            $this->centers->updateImportance($value['id'],  $value['importance']);
        }

        return response() ->json();
    }

    public function import(Request $form)
    {
      
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');   
     
        $err_msg=$this->centers->importCenters($file,$this->currentUserId());
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
