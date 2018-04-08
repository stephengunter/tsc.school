<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
   

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    function username()
    {
        return 'name';
    }
   
    public function showLoginForm()
    {
        $request=request();

        $intend='';
        if($request->intend)  $intend=$request->intend;
        if($intend) $intend= urldecode($intend);

        $model=[
            'title' => '登入',
            'topMenus' => $this->clientMenus(),
            'intend' => $intend
           
        ];
        return view('auth.login')->with($model);
    }

    public function login(LoginRequest $request)
    {
        
        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();
            return response() ->json();
        }

        $errors=[
            'msg' => ['登入失敗']
        ];
        
        return $this->requestError($errors);
        
    }

    function attemptLogin(LoginRequest $request)
    {
        $request=$request->getValues();

        $name=$request['email'];
        $password=$request['password'];
        $remember=$request['remember'];
       

        return $this->guard()->attempt(['name' => $name, 'password' => $password], $remember); 
    }
}
