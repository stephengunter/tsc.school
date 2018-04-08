<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;


use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function __construct()
    {
        
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
