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
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use App\Core\Addresses;
use Illuminate\Support\Facades\Input;

class CentersController extends Controller
{
    use Addresses;
    
    public function __construct(Centers $centers,Files $files)
    {
        $this->centers=$centers;
        $this->files=$files;
    }

    function keyOptions($withOversea=false,$withEmpty=false)
    {
        
        $options=$this->centers->getKeyOptions($withOversea);
        if($withEmpty) array_unshift($options, ['text' => '----------' , 'value' => '']);
        
        return $options;
    }

    function canEdit()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser->admin->isHeadCenterAdmin();
    }
    function canDelete(Center $center)
    {
        if(!$this->canEdit()) return false;
        if($center->active) return false;
        return true;
    }

    function canImport()
    {
        return $this->currentUserIsDev();
        return $this->currentUser->admin->isHeadCenterAdmin();
    }

    

    
    public function index()
    {
       
        $request=request();

        
        $key='';
        if($request->key)  $key = $request->key;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);

        $keys=['west','east'];
        if(!in_array($key,$keys)) $key='';

        $centers=$this->centers->fetchCenters($key);
        
       
        $centers=$this->centers->getOrdered($centers);
       
      
        $pageList = new PagedList($centers);

        foreach($pageList->viewList as $center){
            $center->loadContactInfo();
        } 

        if($this->isAjaxRequest()){
            return response()->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');

        $withOversea=false;
        $withEmpty=true;
        $keyOptions=$this->keyOptions($withOversea,$withEmpty);
       
        return view('centers.index')->with([
            'title' => '開課中心管理',
            'menus' => $menus,
            'key' => $key,
            'keys' => $keyOptions,
            'areas' => $this->areaOptions(),
            'canEdit' => $this->canEdit(),
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $keyOptions=$this->keyOptions();
        $areaOptions=$this->areaOptions();
        $cityOptions=$this->cityOptions();
        $center=Center::init();
       

        $center['contactInfo']['address']['cityId']=$cityOptions->first()->id;
      
        $form=[
            'center' => $center,
            'keyOptions' => $keyOptions,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
        ];

        return response() ->json($form);
      
    }

    public function store(CenterRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $center=new Center($request->getCenterValues());
        $contactInfoValues=$request->getContactInfoValues();
        $addressValues=$request->getAddressValues();
        
        $updatedBy=$current_user->id;
        $center->updatedBy=$updatedBy;
        $contactInfoValues['updatedBy']=$updatedBy;
        $addressValues['updatedBy']=$updatedBy;

        if($center->key =='oversea')  $center->areaId = null;

        $center = $this->centers->createCenter($center,$contactInfoValues,$addressValues);
        return response() ->json($center);
    }

    public function show($id)
    {
        $center = $this->centers->getById($id);
        if(!$center) abort(404);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $center->loadViewModel();
        $center->canEdit=$this->canEdit();
        $center->canDelete=$this->canDelete($center);

        if($center->contactInfo)  $center->contactInfo->canEdit= $center->canEdit;
       

        return response() ->json($center);
        
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $center = Center::findOrFail($id);

        $areaOptions= $this->areaOptions();
        $cityOptions=$this->cityOptions();
        $keyOptions=$this->keyOptions();
        
      
        $form=[
            'center' => $center,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
            'keyOptions' => $keyOptions,
        ];

        return response() ->json($form);
       
        
    }


    public function update(CenterRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $center = Center::findOrFail($id);
       
        $values=$request->getCenterValues();

        if($values['key']=='oversea')  $values['areaId'] = null;

        $values['updatedBy'] = $current_user->id;
        
        $center->update($values);
        return response() ->json();
    }

    public function destroy($id) 
    {
        $center = Center::findOrFail($id);
        if(!$this->canDelete($center)) return $this->unauthorized();

        $center->updatedBy=$this->currentUserId();
        $center->removed=true;
        $center->save();
       
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

    public function upload(Request $form)
    {
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);

        $type=$form['type'];
        if(!$type) abort(500);

        $file=Input::file('file');  

       

        $this->files->saveUploadsData($file,$type);

        return response() ->json();
        
       
    }
}
