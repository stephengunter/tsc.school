<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;

use App\Admin;
use App\Center;
use App\Services\Admins;
use App\Services\Centers;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class AdminsController extends Controller
{
    
    public function __construct(Admins $admins, Centers $centers)
    {
        $this->admins=$admins;
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
   
    function centerOptions()
    {
        $localCenters= $this->centers->getLocalCenters(true)->get();

        $options = $localCenters->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        array_unshift($options, ['text' => '所有中心' , 'value' =>'0']);
        return $options;
    }

    function loadCenterNames($admin)
    {
        if(count($admin->centers)){
            $admin->centerNames=join(',',$admin->centers->pluck('name')->toArray() );
        }else{
            $admin->centerNames='';
        }
       
        
    }
    function loadRoleNames($admin)
    {
        if(count($admin->user->roles)){
          
            $admin->user->roleNames=join(',',$admin->user->roles->pluck('name')->toArray() );
        }else{
            $admin->user->roleNames='';
        }
       
        
    }
 
    
    public function index()
    {
     

        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=0;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);
        if ($selectedCenter == null)
        {
            $center = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        $admins =  $this->admins->fetchAdmins($selectedCenter, $keyword);
      
        $pageList = new PagedList($admins);
        

        foreach($pageList->viewList as $admin){
            $this->loadCenterNames($admin);
            $this->loadRoleNames($admin);
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

       
        return view('admins.index')->with([
            'title' => '權限管理',
            'menus' => $menus,
            'centers' => $this->centerOptions(),
           
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $areaOptions=$this->areaOptions();
        $cityOptions=$this->cityOptions();
        $admin=Admin::init();
       

        $admin['contactInfo']['address']['cityId']=$cityOptions->first()->id;
      
        $form=[
            'admin' => $admin,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
        ];

        return response() ->json($form);
      
    }

    public function store(AdminRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $admin=new Admin($request->getAdminValues());
        $contactInfo=new ContactInfo($request->getContactInfoValues());
        $address=new Address($request->getAddressValues());
        
        $updatedBy=$current_user->id;
        $admin->updatedBy=$updatedBy;
        $contactInfo->updatedBy=$updatedBy;
        $address->updatedBy=$updatedBy;

        $admin = $this->admins->createAdmin($admin,$contactInfo,$address);
        return response() ->json($admin);
    }

    public function show($id)
    {
        $admin = $this->admins->getById($id);
        if(!$admin) abort(404);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $admin->loadContactInfo();
        $admin->canEdit=$this->canEdit();

        if($admin->contactInfo)  $admin->contactInfo->canEdit= $admin->canEdit;
       

        return response() ->json($admin);
        
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $admin = Admin::findOrFail($id);

        $areaOptions= $this->areaOptions();
        $cityOptions=$this->cityOptions();
      
        $form=[
            'admin' => $admin,
            'areaOptions' => $areaOptions,
            'cityOptions' => $cityOptions,
        ];

        return response() ->json($form);
       
        
    }


    public function update(AdminRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $admin = Admin::findOrFail($id);
       
        $values=$request->getAdminValues();

        $values['updatedBy'] = $current_user->id;
        
        $admin->update($values);
        return response() ->json();
    }

    public function importances(Request $request)
    {
        $values=$request->get('importances');
        foreach ($values as $value)
        {
            $value['id'];
            $value['importance'];
            $this->admins->updateImportance($value['id'],  $value['importance']);
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
     
        $err_msg=$this->admins->importAdmins($file,$this->currentUserId());
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
