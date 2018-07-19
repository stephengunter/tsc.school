<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;

use App\Admin;
use App\User;
use App\Profile;
use App\Center;
use App\Role;
use App\Services\Admins;
use App\Services\Users;
use App\Services\Centers;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class AdminsController extends Controller
{
    
    public function __construct(Admins $admins, Users $users,Centers $centers,Files $files)
    {
        $this->admins=$admins;
        $this->users=$users;
        $this->centers=$centers;
        $this->files=$files;
    }

    function canEdit($admin)
    {
        if($this->currentUserIsDev()) return true;
        if(!count($admin->centers)) return true;

        return $this->canAdminCenters($admin->centers);


    }
    function canEditCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        return $this->canAdminCenter($center);

    }
    function canDelete($admin)
    {
        return $this->canEdit($admin);
    }


    function canImport()
    {
        return $this->currentUserIsDev();
    }

    function canEditCenters()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->admin->isHeadCenterAdmin();
    }

   
    function roleOptions()
    {
        return [
            ['text'=>'職員', 'value'=> Role::staffRoleName()],
            ['text'=>'主管', 'value'=> Role::bossRoleName()]
           
        ];
    }
   
    function centerOptions()
    {
        $centers= $this->centersCanAdmin();

        $options = $centers->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

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

        $page=1;
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
      
        $pageList = new PagedList($admins,$page,$pageSize);
        

        foreach($pageList->viewList as $admin){
            $admin->user->loadContactInfo();
            $this->loadCenterNames($admin);
            $this->loadRoleNames($admin);
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

        $centers=$this->centers->centerOptions();
       
        return view('admins.index')->with([
            'title' => '權限管理',
            'menus' => $menus,
            'centers' => $centers,
           
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $admin=Admin::init();
        $user=User::init();
        $roleOptions=$this->roleOptions();
      
        $centerOptions = $this->centerOptions();
        $centerIds=[];
        if (count($centerOptions))
        {
            array_push($centerIds,$centerOptions[0]['value']);
        }
      
        $form=[
            'admin' => $admin,
            'role' => Role::staffRoleName(),
            'user' => $user,
            'roleOptions' => $roleOptions,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
      
    }

    public function store(AdminRequest $request)
    {

        $adminValues=$request->getAdminValues();
        $userValues=$request->getUserValues();
        $profileValues= $userValues['profile'];

        $role = $request['role'];
      
        $errors=$this->users->validateUserInputs($userValues,$role);
        if($errors) return $this->requestError($errors);
        

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
            return $this->requestError($errors);
        }

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $adminValues['updatedBy']=$updatedBy;
        $userValues['updatedBy']=$updatedBy;
        $profileValues['updatedBy']=$updatedBy;

        
        
        $userValues=array_except($userValues,['profile','roles']);
        $userId=$request->getUserId();

       
        $user=null;
        if($userId){
            $user = User::find($userId);
            
            $user->updateProfile($profileValues);
            $user->update($userValues);
            
        }else{
          
           $user=$this->users->createUser(new User($userValues),new Profile($profileValues));
           $userId=$user->id;
         
        }

        $admin=Admin::find($userId);
        if($admin){
            $admin->update($adminValues);
            $admin->setRole($role);
            $admin->centers()->sync($centerIds);
        }else{
            $admin=$this->admins->createAdmin($user,new Admin($adminValues),$role,$centerIds);
            $admin->userId=$userId;
        }
       
        return response() ->json($admin);
    }

    public function show($id)
    {
        $admin = $this->admins->getById($id);
        if(!$admin) abort(404);

        $current_user=$this->currentUser();

        $this->loadCenterNames($admin);
        $this->loadRoleNames($admin);

        $admin->user->loadContactInfo();
     
        $admin->canEdit=$this->canEdit($admin);
        $admin->canDelete=$this->canDelete($admin);
       

        return response() ->json($admin);
        
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        
        if(!$this->canEdit($admin)) $this->unauthorized();

        $roleOptions= $this->roleOptions();

      
        $role='';
        if($admin->user->isBoss())  $role=Role::bossRoleName();
        else if ($admin->user->isStaff())  $role=Role::staffRoleName();

       
        $centerIds=[];
        $centerOptions=[];
        if ($this->canEditCenters())
        {
            $centerIds = $admin->centers->pluck('id')->toArray();
          
            $centerOptions = $this->centerOptions();
        }

      
        $form=[
            'admin' => $admin,
            'roleOptions' => $roleOptions,
            'role' => $role,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
       
        
    }


    public function update(AdminRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        if(!$this->canEdit($admin)) $this->unauthorized();
       
        $values=$request->getAdminValues();
        $roleName=$request->getRole();

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;
        $admin->update($values);

        $admin->setRole($roleName);
     
        if ($this->canEditCenters())
        {
            $centerIds=$request->getCenterIds();
            if(!count($centerIds)){
                $errors['centerIds'] = ['請選擇所屬中心'];
                return $this->requestError($errors);
            }

            $admin->centers()->sync($centerIds);
        }

        return response() ->json();
    }

    public function destroy($id) 
    {
        $admin = Admin::findOrFail($id);
        if(!$this->canDelete($admin)) $this->unauthorized();

        $this->admins->deleteAdmin($admin);
       
       
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

        $center = Center::findOrFail($form['center']);  

        $canEdit = $this->canEditCenter($center);
        if(!$canEdit) return $this->unauthorized();

       

        $this->files->saveUploadsData($file,$type,$center);

        return response() ->json();
        
       
    }
}
