<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

use App\User;
use App\Role;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller
{
    public function __construct(Users $users)
    {
        $this->users=$users;
    }

    function canEdit($user)
    {
        if($this->currentUserIsDev()) return true;
        return false;
    }

    function canDelete($user)
    {
        return $this->canEdit($user);
    }

    
    function loadRoleNames($user)
    {
        if(count($user->roles)){
            $user->roleNames=join(',',$user->roles->pluck('name')->toArray() );
        }else{
            $user->roleNames='';
        }
       
        
    }
    
    public function index()
    {

        $request=request();

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $role = null;

        $users =  $this->users->fetchUsers($role, $keyword);

        $users=$this->users->getOrdered($users);
      
        $pageList = new PagedList($users,$page,$pageSize);
        

        foreach($pageList->viewList as $user){
            $this->loadRoleNames($user);
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');
       
        return view('users.index')->with([
            'title' => '使用者管理',
            'menus' => $menus,
       
            'list' =>  $pageList
        ]);
    }
    
    public function show($id)
    {
        $user = $this->users->getById($id);
        if(!$user) abort(404);

        $current_user=$this->currentUser();

        $this->loadRoleNames($user);

        $user->loadContactInfo();
     
        $user->canEdit=$this->canEdit($user);
        $user->canDelete=$this->canDelete($user);
       

        return response() ->json($user);
        
    }



    public function edit($id)
    {
        $user = $this->users->getById($id);
        if(!$user) abort(404);
      
        $form=[
            'user' => $user
        ];

        return response() ->json($form);
        
    }


    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if(!$this->canEdit($user)) $this->unauthorized();

        $userValuesWithProfile=$request->getUserValues(true);
        $roleName=$request['role'];
        if(!$roleName) $roleName='';

        if($user->isBoss()) $roleName=Role::bossRoleName();
        else if($user->isStaff()) $roleName=Role::staffRoleName();
        else if($user->isTeacher()) $roleName=Role::teacherRoleName();
        else if($user->isStudent()) $roleName=Role::isStudent();

        $errors=$this->users->validateUserInputs($userValuesWithProfile, $roleName);

        if($errors) return $this->requestError($errors);

        $userValues=$request->getUserValues();
        $profileValues=$request->getProfileValues();

       

        $current_user=$this->currentUser();
        $userValues['updatedBy'] = $current_user->id;
        $profileValues['updatedBy'] = $current_user->id;

       
        $user->profile->update($profileValues);
        
        $this->users->updateUser($user,$userValues);

        return response() ->json();
    }

    public function find(UserRequest $request)
    {
        $email=$request['email'];
        $phone=$request['phone'];
        $sid=$request['profile']['sid'];
        
        $users=$this->users->findUsers($email, $phone, $sid);

        $pageList = new PagedList($users);

        
        
        foreach($pageList->viewList as $user){
            
            $this->loadRoleNames($user);
        } 
        
        
        return response() ->json($pageList);
    }

    
}
