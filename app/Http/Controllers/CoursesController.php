<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;

use App\Course;
use App\User;
use App\Profile;
use App\Center;
use App\Role;
use App\Services\Courses;
use App\Services\CourseGroups;
use App\Services\Users;
use App\Services\Centers;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CoursesController extends Controller
{
    
    public function __construct(Courses $courses, Users $users,Centers $centers ,CourseGroups $courseGroups)
    {
        $this->courses=$courses;
        $this->users=$users;
        $this->centers=$centers;
        $this->courseGroups=$courseGroups;
    }

    function canEdit($course)
    {
        if($this->currentUserIsDev()) return true;
        if(!count($course->centers)) return true;
      
        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect($course->centers);

        if(count($intersect)) return true;
        return false;

    }

    function canReview(Course $course)
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->isBoss();
    }
    function canReviewCenter(Center $center)
    {
        if(!$center) return false;

        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$center]);

        if(count($intersect)) return true;
        return false;
    }

    function canDelete($course)
    {
        return $this->canReview($course);
    }


    function canImport()
    {
        return $this->currentUserIsDev();
    }

    function canEditCenters()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->admin->isHeadCenterCourse();
    }


    function loadCenterNames($course)
    {
        if(count($course->centers)){
            $course->centerNames=join(',',$course->centers->pluck('name')->toArray() );
        }else{
            $course->centerNames='';
        }
       
        
    }
    function loadRoleNames($course)
    {
        if(count($course->user->roles)){
          
            $course->user->roleNames=join(',',$course->user->roles->pluck('name')->toArray() );
        }else{
            $course->user->roleNames='';
        }
       
        
    }
    function loadWage($course)
    {
        $wage = $course->getWage();
        if($wage){
            $course->wage=$wage->money;
            $course->account=$wage->account;
        }else{
            $course->wage=0;
            $course->account='';
        }
        
    }
 
   
    public function index()
    {
        $request=request();

        $group=false;
        if($request->group)  $group=Helper::isTrue($request->group);
      

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);
        if ($selectedCenter == null)
        {
            $center = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        if($group){
            return $this->courseGroupsIndex($selectedCenter, $keyword ,$page, $pageSize);
        }

        $canReview=false;
        if($selectedCenter)   $canReview=$this->canReviewCenter($selectedCenter);
      
        $courses =  $this->courses->fetchCourses($selectedCenter, $reviewed, $keyword);
      
        $pageList = new PagedList($courses,$page,$pageSize);
        
        if (!$selectedCenter)
        {
            foreach($pageList->viewList as $course){
                $this->loadCenterNames($course);
                $course->user->loadContactInfo();
            } 
        }
       

        if($this->isAjaxRequest()){
            return response() ->json([
                'canReview' => $canReview,
                'model' => $pageList
            ]);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

        $centers=$this->centers->centerOptions();
       
       
        return view('courses.index')->with([
            'title' => '教師管理',
            'menus' => $menus,
            'centers' => $centers,
            'canReview' => $canReview,
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    function courseGroupsIndex($selectedCenter, $keyword ,$page, $pageSize)
    {
        $courses =  $this->courseGroups->fetchCourseGroups($selectedCenter, $keyword);
      
        $pageList = new PagedList($courses,$page,$pageSize);
        
        if (!$selectedCenter)
        {
            foreach($pageList->viewList as $courseGroup){
                $courseGroup->getCourseNames();
                $courseGroup->centerName=$courseGroup->center->name;
            } 
        }

        $canReview=false;
        if($selectedCenter)   $canReview=$this->canReviewCenter($selectedCenter);

        if($this->isAjaxRequest()){
            return response() ->json([
                'canReview' => $canReview,
                'model' => $pageList
            ]);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

        

        $centers=$this->centers->centerOptions();
       
       
        return view('courses.index')->with([
            'title' => '教師管理',
            'menus' => $menus,
            'centers' => $centers,
            'canReview' => $canReview,
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

   

    public function create()
    {
        $course=Course::init();
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
            'course' => $course,
            'user' => $user,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
      
    }

    public function validateCourseInputs($values)
    {
        $errors=[];

        $group=false;
        if(array_key_exists('group',$values)) $group=Helper::isTrue($values['group']);
        
        if($group){


        }else{
            $wage=0;
            if($values['wage']) $wage=floatval($values['wage']);
            if(!$wage) 	$errors['course.wage'] = ['必須填寫鐘點費'];

            if(!$values['account']) 	$errors['course.account'] = ['必須填寫銀行帳號'];

        }

        return $errors;
    }

    public function store(CourseRequest $request)
    {

        $courseValues=$request->getCourseValues();
        $userValues=$request->getUserValues();
        $profileValues= $userValues['profile'];

        
        $errors=$this->users->validateUserInputs($userValues);
        if($errors) return $this->requestError($errors);

        $errors=$this->validateCourseInputs($courseValues);

        $sid=$userValues['profile']['sid'];
        if(!$sid){
            $errors['user.profile.sid'] = ['必須填寫身分證號'];
        }

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

      
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $courseValues['updatedBy']=$updatedBy;
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

        $wageValues=[
            'account' => $courseValues['account'],
            'money' => $courseValues['wage'],
            'updatedBy' => $updatedBy,
        ];

        $course=Course::find($userId);
        if($course){
            $course->update($courseValues);
            $course->addRole();

            $wage=$course->getWage();
            if($wage) $wage->update($wageValues);

        }else{
            $course=$this->courses->createCourse($user,new Course($courseValues), $wageValues);
            $course->userId=$userId;
        }

        $course->centers()->sync($centerIds);
       
        return response() ->json($course);
    }

    public function show($id)
    {
        $course = $this->courses->getById($id);
        if(!$course) abort(404);

        $current_user=$this->currentUser();

        $this->loadCenterNames($course);
        $this->loadRoleNames($course);

        $this->loadWage($course);

        $course->user->loadContactInfo();
     
        $course->canEdit=$this->canEdit($course);
        $course->canDelete=$this->canDelete($course);
       

        return response() ->json($course);
        
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);        
        if(!$this->canEdit($course)) $this->unauthorized();

        $this->loadWage($course);
       
        $centerIds=$course->centers->pluck('id')->toArray();

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

      
        $form=[
            'course' => $course,
         
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
       
        
    }


    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        if(!$this->canEdit($course)) $this->unauthorized();
       
        $values=$request->getCourseValues();
     
        $errors=$this->validateCourseInputs($values);

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;

        $course->update($values);

        $wageValues=[
            'account' => $values['account'],
            'money' => $values['wage'],
            'updatedBy' =>  $current_user->id
        ];

        $course->setWage($wageValues);
     
        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
            return $this->requestError($errors);
        }

        $course->centers()->sync($centerIds);

        return response() ->json();
    }

    public function review(Request $form)
    {
        $reviewedBy=$this->currentUserId();
        
        $courses=  $form['courses'];

        if(count($courses) > 1){
            $courseIds=array_pluck($courses, 'id');
            $this->courses->reviewOK($courseIds, $reviewedBy);
        }else{
            
            $id=$courses[0]['id'];
         
            $reviewed=Helper::isTrue($courses[0]['reviewed']);

            $this->courses->updateReview($id,$reviewed ,$reviewedBy);
        }
        

        return response() ->json();


    }

    public function destroy($id) 
    {
        $course = Course::findOrFail($id);
        if(!$this->canDelete($course)) $this->unauthorized();

        $this->courses->deleteCourse($course, $this->currentUserId());
       
       
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

        $group=Helper::isTrue($form['group']);
        if($group){
            $err_msg=$this->courseGroups->importCourseGroups($file,$this->currentUserId());
        }else{
            
            $err_msg=$this->courses->importCourses($file,$this->currentUserId());
        
        }

     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
