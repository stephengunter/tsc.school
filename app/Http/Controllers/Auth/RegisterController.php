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
use App\Core\Helper;

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
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany()
           
        ];

        

        return view('auth.register')->with($model);
    } 
   
    

    public function register(RegisterRequest $request)
    {
        //event(new Registered($user = $this->create($request->all())));
        $values=$request->getValues();

        $sid=$values['sid'];
        $errors=[];
        if(Helper::isTaiwanSID($sid)){
            $isValid=Helper::checkSID($sid);
            if(!$isValid){
                $errors['sid'] = ['身分證號錯誤'];
                return $this->requestError($errors);
            }
           
        }

        $existUser=$this->users->findBySID($sid);
        if($existUser){
            $errors['sid'] = ['身分證號重複了'];
            return $this->requestError($errors);
        } 
       
        $user=new User([
           
            'email' => $values['email'],
            'phone' => $values['phone'],
            'password' => $values['password'],
        ]);
        $profile=new Profile([
            'sid' => $values['sid'],
            'fullname' => $values['fullname'],
            'dob' => $values['dob'],
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
