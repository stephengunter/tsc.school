<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ManageLoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckAdmin;
use Auth;

class SessionsController extends Controller
{
    public function __construct() {
		$this->middleware('guest', ['except' => 'destroy']);
	}

    public function create()
    {  
		
        return view('auth.manage-login');
    }

	public function store(ManageLoginRequest $request)
    {  

		$values=[
			'name' => strtoupper($request['name']),
			'password' => $request['password'],
		];

		
	
		if (!Auth::attempt($values)) {
            return   response()->json(['msg' => '登入失敗' ]  ,  422);
		}

		$user=Auth::user();

		$can_login= CheckAdmin::canLogin($user);
		
		if($can_login){
            return response()->json();  
        }else{
            return CheckAdmin::exceptions($user);
        }     
                
	}

	public function destroy() {
		
		auth()->logout();
        if(!request()->ajax())  return redirect('/manage/login');
		return response()->json();   
               
	}
}
