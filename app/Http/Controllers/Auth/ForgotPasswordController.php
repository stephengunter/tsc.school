<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Requests\SendResetEmailRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    protected $redirectTo = '/';

    use SendsPasswordResetEmails;

    
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $model=[
            'title' => '忘記密碼',
            'topMenus' => $this->clientMenus()
           
        ];
        return view('auth.passwords.email')->with($model);
    }

    public function sendResetLinkEmail(SendResetEmailRequest $request)
    {
       
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
