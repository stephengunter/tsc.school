<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Requests\SendResetEmailRequest;
use Illuminate\Support\Facades\Password;
use App\User;
use App\Services\Users;

class ForgotPasswordController extends Controller
{
    protected $redirectTo = '/';

    use SendsPasswordResetEmails;

    
    public function __construct(Users $users)
    {
      
        $this->middleware('guest');
       
        $this->users=$users;
    }

    public function showLinkRequestForm()
    {
       
        $model=[
            'title' => '忘記密碼',
            'topMenus' => $this->clientMenus(), 
            'company' => $this->getCompany()
           
        ];
        return view('auth.passwords.email')->with($model);
    }

    public function sendResetLinkEmail(SendResetEmailRequest $request)
    {
        $sid=$request['name'];
        $email=$request['email'];
        
        $user=$this->users->findBySID($sid);
        if(!$user) abort(404);
        if(!$user->email==$email) abort(404);
       
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if($response == Password::RESET_LINK_SENT)
        {
            return response()->json();
        }

        $errors['email'] = ['您輸入的Email不存在.請確認輸入您在本站註冊的Email'];
        return $this->requestError($errors);
    }
}
