<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VolunteerRequest;

use App\Volunteer;
use App\User;
use App\Profile;
use App\Center;
use App\Role;
use App\Weekday;
use App\Services\Volunteers;
use App\Services\Users;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\CourseInfoes;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class VolunteersController extends Controller
{
    
    public function __construct(Volunteers $volunteers ,Users $users,
        Centers $centers ,Courses $courses,CourseInfoes $courseInfoes,Files $files)
    {
        $this->volunteers=$volunteers;
        $this->users=$users;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->courseInfoes=$courseInfoes;
        $this->files=$files;
       
    }

    function canImport()
    {
        return $this->currentUserIsDev();
    }
    
    public function index()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $weekday=0;
        if($request->weekday)  $weekday=(int)$request->weekday;

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);
        if (!$selectedCenter)
        {
            $center = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        $selectedWeekday = null;
        if ($weekday) $selectedWeekday = Weekday::find($weekday);


        
        $volunteers =  $this->volunteers->fetchVolunteers($selectedCenter,$selectedWeekday,$keyword);
      
        $pageList = new PagedList($volunteers,$page,$pageSize);
        
        foreach($pageList->viewList as $volunteer){
            $volunteer->loadViewModel();
            $volunteer->user->loadContactInfo();
        } 
       

        if($this->isAjaxRequest()){
           
            return response() ->json([
                'model' => $pageList
            ]);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');
        $centers=$this->centers->centerOptions();
       
        return view('volunteers.index')->with([
            'title' => '志工管理',
            'menus' => $menus,
            'centers' => $centers,
            'weekdays' => $this->courseInfoes->weekdayOptions('可服務時間'),
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $volunteer=Volunteer::init();
        $user=User::init();

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $centerIds=[];
        if (count($centerOptions))
        {
            array_push($centerIds,$centerOptions[0]['value']);
        }
      
        $form=[
            'volunteer' => $volunteer,
            'user' => $user,

            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds,

        ];

        return response() ->json($form);
      
    }

    public function validateVolunteerInputs($values)
    {
        $errors=[];

        return $errors;
    }

    public function store(VolunteerRequest $request)
    {

        $volunteerValues=$request->getVolunteerValues();
        $userValues=$request->getUserValues();
        $profileValues= $userValues['profile'];

        
        $errors=$this->users->validateUserInputs($userValues,Role::volunteerRoleName());
        if($errors) return $this->requestError($errors);

        $errors=$this->validateVolunteerInputs($volunteerValues);
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $volunteerValues['updatedBy']=$updatedBy;
        $userValues['updatedBy']=$updatedBy;
        $profileValues['updatedBy']=$updatedBy;
        
        $userValues=array_except($userValues,['profile']);
        $userId=$request->getUserId();
        $user=null;
        if($userId){
            $user = User::find($userId);

            $user->profile->update($profileValues);
            $this->users->updateUser($user,$userValues);
            
        }else{
          
           $user=$this->users-> createUser(new User($userValues),new Profile($profileValues));
           $userId=$user->id;
         
        }


        $volunteer=Volunteer::find($userId);
        if($volunteer){
            $volunteer->update($volunteerValues);
            $volunteer->addRole();

        }else{
            $volunteer=$this->volunteers->createVolunteer($user,new Volunteer($volunteerValues));
            $volunteer->userId=$userId;
        }

      
       
        return response() ->json($volunteer);
    }

    public function show($id)
    {
        $volunteer = $this->volunteers->getById($id);
        if(!$volunteer) abort(404);

        $current_user=$this->currentUser();
        $volunteer->user->loadContactInfo();

        $volunteer->canEdit=true;
        $volunteer->canDelete=true;

        return response() ->json($volunteer);
        
    }

    public function edit($id)
    {
        $volunteer = Volunteer::findOrFail($id);   
       
       
        $form=[
            'volunteer' => $volunteer
        ];

        return response() ->json($form);
       
        
    }


    public function update(VolunteerRequest $request, $id)
    {
        $volunteer = Volunteer::findOrFail($id);
       
        $values=$request->getVolunteerValues();
     
        $errors=$this->validateVolunteerInputs($values);
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;

        $volunteer->update($values);

        return response() ->json();
    }

   

    public function destroy($id) 
    {
        $volunteer = Volunteer::findOrFail($id);

        $this->volunteers->deleteVolunteer($volunteer, $this->currentUserId());
       
       
        return response() ->json();
    }

    public function import(Request $form)
    {
        
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');   

        $err_msg=$this->volunteers->importVolunteers($file,$this->currentUserId());
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }

    public function upload(Request $form)
    {
       
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);

        $type=$form['type'];
        if(!$type) abort(500);

        $file=Input::file('file');  

        $center = Center::findOrFail($form['center']);  
       
       

        $this->files->saveUploadsData($file,$type,$center);

        return response() ->json();
        
       
    }


}
