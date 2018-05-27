<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

use App\User;
use App\Role;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller
{
    public function __construct(Users $users)
    {
        $this->users=$users;
    }

    function canEdit(User $user)
    {
        if($this->currentUserIsDev()) return true;

        if($user->teacher){
            if(!count($user->teacher->centers)) return true;

            return $this->canAdminCenters($user->teacher->centers);
        }
        return true;
    }

    function canDelete($user)
    {
        return $this->canEdit($user);
    }

    
    function loadRoleNames($user)
    {
        if(count($user->roles)){
            $user->roleNames=join(',',$user->roles->pluck('name')->toArray() );
        }else{
            $user->roleNames='';
        }
       
        
    }

    public function seed()
    {
        $roads=[ '中正','中華','民生','建國','忠孝', '仁愛','信義','和平'];
        
        $districts=\App\District::all()->pluck('id')->toArray();

        $faker = \Faker\Factory::create();
		
		foreach(range(1, 50) as $i) {
			$gender=( $i %2 == 0 );
			$sid= $faker->randomLetter();
			$sid .= $gender ? '1' : '2' ;
			$sid .= mt_rand(1, 99999999);
            $user = new User([
                
                'email' => $faker->unique()->safeEmail,
				'phone' =>  '093' . mt_rand(1, 9999999)
            ]);

            $profile=new \App\Profile([
				'fullname'=> $faker->name,
				'sid' => $sid,
                'dob' => mt_rand(1945, 1995) . '-' .mt_rand(1, 12).'-'.mt_rand(1, 28),
                'gender' => ( $i %2 == 0 ),
            ]);

			$user=$this->users->createUser($user,$profile);
			
            
            $address=new \App\Address([
                'districtId' => array_rand($districts, 1),
                'street' => $roads[array_rand($roads, 1)] . '路' . mt_rand(1, 300) . '號',
            ]);

            $contactInfo=new \App\ContactInfo([
                'tel' => $faker->tollFreePhoneNumber     
            ]);

            $this->users->setContactInfo($user,$contactInfo,$address);
            
		}  
    }
    
    public function index()
    {

        $request=request();

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $role = null;

        $users =  $this->users->fetchUsers($role, $keyword);

        $users=$this->users->getOrdered($users);
      
        $pageList = new PagedList($users,$page,$pageSize);
        

        foreach($pageList->viewList as $user){
            $user->loadViewModel();
            $this->loadRoleNames($user);
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       
         
        $menus=$this->adminMenus('UsersAdmin');
       
        return view('users.index')->with([
            'title' => '使用者管理',
            'menus' => $menus,
       
            'list' =>  $pageList
        ]);
    }
    
    public function show($id)
    {
        $user = $this->users->getById($id);
        if(!$user) abort(404);

        $current_user=$this->currentUser();

        $this->loadRoleNames($user);

        $user->loadContactInfo();
     
        $user->canEdit=$this->canEdit($user);
        $user->canDelete=$this->canDelete($user);
       

        return response() ->json($user);
        
    }



    public function edit($id)
    {
        
        $user = $this->users->getById($id);
        if(!$user) abort(404);
      
        $form=[
            'user' => $user
        ];

        return response() ->json($form);
        
    }


    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if(!$this->canEdit($user)) return $this->unauthorized();

        

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
        $userValues['updatedBy'] = $current_user->id;
        $profileValues['updatedBy'] = $current_user->id;

       
        $user->profile->update($profileValues);
        
        $this->users->updateUser($user,$userValues);

        return response() ->json();
    }

    public function find(UserRequest $request)
    {
        
        $sid=$request['profile']['sid'];
        $email='';
        $phone='';
      
        $users=$this->users->findUsers($email, $phone, $sid);

        $pageList = new PagedList($users);
        
        foreach($pageList->viewList as $user){
            
            $this->loadRoleNames($user);
        } 
        
        
        return response()->json($pageList);
    }

    
}
