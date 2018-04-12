<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
       
        $model=[
            'title' => '重設密碼',
            'topMenus' => $this->clientMenus(),
            'token' => $token
           
        ];

        return view('auth.passwords.reset')->with($model);
       
    }

    public function reset(ResetPasswordRequest $request)
    {
       
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

     

        if($response == Password::PASSWORD_RESET)
        {
            return response()->json();
        }

        $errors['token'] = ['重設密碼失敗.建議您重新開始密碼重設流程'];
        
        return $this->requestError($errors);



        
    }
}
