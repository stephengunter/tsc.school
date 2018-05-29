<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

use App\User;
use App\Role;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;


use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function __construct(Users $users)
    {
        $this->users=$users;
    }

    public function show()
    {
        $id = $this->currentUserId();
        $user = $this->users->getById($id);
       
        if(!$user) abort(404);

        if($this->isAjaxRequest()){
            return response() ->json($user);
        }

        $model=[
            'title' => '個人資訊',
            'topMenus' => $this->clientMenus(),
            'user' => $user
        ];

        return view('client.users.profile')->with($model);
    }

    public function edit()
    {
       
        $id = $this->currentUserId();
        $user = $this->users->getById($id);
        if(!$user) abort(404);
      
        $form=[
            'user' => $user
        ];

        return response() ->json($form);

       
        
    }

    public function update(UserRequest $request)
    {
        $id = $this->currentUserId();
        $user = $this->users->getById($id);

        $userValuesWithProfile=$request->getUserValues(true);
       
        $roleName=$request['role'];
        if(!$roleName) $roleName='';

        if($user->isBoss()) $roleName=Role::bossRoleName();
        else if($user->isStaff()) $roleName=Role::staffRoleName();
        else if($user->isTeacher()) $roleName=Role::teacherRoleName();
        else if($user->isStudent()) $roleName=Role::studentRoleName();

       

        $errors=$this->users->validateUserInputs($userValuesWithProfile, $roleName);

        if($errors) return $this->requestError($errors);

        $userValues=$request->getUserValues();
        $profileValues=$request->getProfileValues();

       

        $current_user=$this->currentUser();
        $userValues['updatedBy'] = $id;
        $profileValues['updatedBy'] = $id;
        
        $this->users->updateUser($user,$userValues,$profileValues);

        return response() ->json();
    }
    
    public function showChangePasswordForm()
    {
        

        $model=[
            'title' => '變更密碼',
            'topMenus' => $this->clientMenus()
           
        ];
        

        return view('client.users.change-password')->with($model);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
       
        $current_user=$this->currentUser();
        $current_password = $current_user->password;           

        if(Hash::check($request['current_password'], $current_password))
        {           
            $user = User ::findOrFail($current_user->id);  
            $user->password = $request['password'];
            $user->save(); 
            return response()->json();   
        }
        else
        {     
            return response()->json([ 'current_password' => ['密碼錯誤']]  ,  422);
        }
    }

   
}
