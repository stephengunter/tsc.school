<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VolunteerRequest;

use App\Volunteer;
use App\User;
use App\ContactInfo;
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
use App\Core\Addresses;
use Illuminate\Support\Facades\Input;

class VolunteersController extends Controller
{
    use Addresses;

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
    function canEdit($volunteer)
    {
        if($this->currentUserIsDev()) return true;
        if(!count($volunteer->centers)) return true;

        return $this->canAdminCenters($volunteer->centers);

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

        $contactInfo = ContactInfo::init();
        $cityOptions=$this->cityOptions();
        $contactInfo['address']['cityId']=$cityOptions->first()->id;
    

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
            'contactInfo' => $contactInfo,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds,
            'cityOptions' => $cityOptions,
            'weekdayOptions' => $this->courseInfoes->weekdayOptions(),
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

        $contactInfoValues= $request->getContactInfoValues();
        $addressValues= $request->getAddressValues();

        
        $errors=$this->users->validateUserInputs($userValues,Role::volunteerRoleName());
        if($errors) return $this->requestError($errors);

        $errors=$this->validateVolunteerInputs($volunteerValues);

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

        $weekdayIds=$request->getWeekdayIds();

        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $volunteerValues['updatedBy']=$updatedBy;
        $userValues['updatedBy']=$updatedBy;
        $profileValues['updatedBy']=$updatedBy;
        $contactInfoValues['updatedBy']=$updatedBy;
        $addressValues['updatedBy']=$updatedBy;
        
        $userValues=array_except($userValues,['profile']);
        $userId=$request->getUserId();

        $user=null;
        if($userId){
            $user = User::find($userId);

            $user->profile->update($profileValues);
            $this->users->updateUser($user,$userValues);
            
        }else{
          
           $user=$this->users->createUser(new User($userValues),new Profile($profileValues));
           $userId=$user->id;
         
        }

        $user->setContactInfo($contactInfoValues,$addressValues);

        
        $volunteerValues['time']=$this->volunteers->getTimeText($volunteerValues['time']);
        $volunteer=Volunteer::find($userId);
        if($volunteer){
            $volunteer->update($volunteerValues);
            $volunteer->addRole();

            $volunteer->weekdays()->sync($weekdayIds);

        }else{
            $volunteer=$this->volunteers->createVolunteer($user,new Volunteer($volunteerValues),$centerIds,$weekdayIds);
            $volunteer->userId=$userId;
            
        }

        
       
        return response() ->json($volunteer);
    }

    public function show($id)
    {
        $volunteer = $this->volunteers->getById($id);
        if(!$volunteer) abort(404);

        $volunteer->loadViewModel();
        $volunteer->user->loadContactInfo();

        $volunteer->canEdit=$this->canEdit($volunteer);
        $volunteer->canDelete=$volunteer->canEdit;

        return response()->json($volunteer);
        
    }

    public function edit($id)
    {
        $volunteer = Volunteer::findOrFail($id);   

        $centerIds=$volunteer->centers()->pluck('id')->toArray();
        $weekdayIds=$volunteer->weekdays()->pluck('id')->toArray();

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $volunteer->time=$this->volunteers->getTimeValue($volunteer->time);
       
        $form=[
            'volunteer' => $volunteer,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds,
            'weekdayOptions' => $this->courseInfoes->weekdayOptions(),
            'weekdayIds' => $weekdayIds,
        ];

        return response() ->json($form);
       
        
    }


    public function update(VolunteerRequest $request, $id)
    {
        $volunteer = Volunteer::findOrFail($id);
        if(!$this->canEdit($volunteer)) return $this->unauthorized();
       
        $values=$request->getVolunteerValues();
     
        $errors=$this->validateVolunteerInputs($values);

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

        $weekdayIds=$request->getWeekdayIds();

        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;

        $values['time']=$this->volunteers->getTimeText($values['time']);

        $volunteer->update($values);
        $volunteer->centers()->sync($centerIds);
        $volunteer->weekdays()->sync($weekdayIds);

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
