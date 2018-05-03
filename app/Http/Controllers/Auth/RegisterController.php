<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Profile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;

use App\Services\Users;

class RegisterController extends Controller
{
    protected $redirectTo = '/';

   
    public function __construct(Users $users)
    {
        $this->middleware('guest');
        $this->users=$users;
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function registered(Request $request, $user)
    {
        //
    }


    public function showRegistrationForm()
    {
        $model=[
            'title' => '註冊 - 建立新帳號',
            'topMenus' => $this->clientMenus()
           
        ];

        

        return view('auth.register')->with($model);
    } 
   
    

    public function register(RegisterRequest $request)
    {
        //event(new Registered($user = $this->create($request->all())));
        $values=$request->getValues();
       
        $user=new User([
            'name' => $values['email'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'password' => Hash::make($values['password']),
        ]);
        $profile=new Profile([
            'fullname' => $values['name'],
            'gender' => $values['gender'],
        ]);

        $this->users->createUser($user,$profile);

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }

    function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
