<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests\ChangePasswordRequest;

//use App\Repositories\Users;
use Illuminate\Support\Facades\Hash;
//use App\Http\Middleware\CheckAdmin;
//use App\Jobs\SendResetPasswordMail;

use App\User;

use App\Http\Controllers\Controller;


class ChangePasswordController extends Controller
{
    public function __construct() 
    {
       
          
    }

    public function index()
    {
        return view('auth.passwords.change');
    }

    public function store(ChangePasswordRequest $request)
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
