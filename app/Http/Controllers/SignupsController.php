<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\SignupInfoRequest;

use App\User;
use App\Term;
use App\Center;
use App\Course;
use App\Signup;
use App\SignupDetail;

use App\Services\Signups;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class SignupsController extends Controller
{
    
    public function __construct(Signups $signups, Discounts $discounts, Users $users,
        Terms $terms,Centers $centers,Courses $courses)
    {
        $this->signups=$signups;
        $this->discounts=$discounts;
        $this->users=$users;
      
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
    }

   
    
   
    public function index()
    {
       
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $course=0;
        if($request->course)  $course=(int)$request->course;

        $status=true;
        if($request->status)  $status=(int)$request->status;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

       
        if($this->isAjaxRequest()){
            return $this->fetchSignups($term, $center, $course,  $status , $page , $pageSize);
        }

        $termOptions = $this->terms->options();
        $centerOptions = $this->centers->options();

        $selectedCenter = null;
        $selectedTerm = null;
        $selectedCourse = null;
        if ($course)
        {
            $selectedCourse = $this->courses->getById($course);
            if (!$selectedCourse) abort(404);
            else
            {
                $selectedCenter = $selectedCourse->center;
                $selectedTerm = $selectedCourse->term;
            }
        }
        else
        {
            if ($center)
            {
                $selectedCenter = $this->centers->getById($center);
                if (!$selectedCenter) abort(404);
            }
            else
            {
                $selectedCenter = $this->centers->getById($centerOptions[0]['value']); 
            }

            if ($term)
            {
                $selectedTerm = $this->terms->getById($term);
                if (!$selectedTerm)  abort(404);
            }
            else
            {
                $selectedTerm = $this->terms->getById($termOptions[0]['value']); 
            }

        }

        if (!$selectedCourse)
        {
            $course = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

       

        $signups = $this->signups->fetchSignups($selectedTerm, $selectedCenter, $selectedCourse);

        $summary=$this->getSignupSummary($signups);

        $signups = $signups->where('status' , $status);

        $pageList = new PagedList($signups,$page,$pageSize);

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'title' => '報名管理',
            'menus' => $this->adminMenus('SignupsAdmin'),

            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,                
            'statuses' => $this->signups->statusOptions(),

            'summary' => $summary,
            'list' => $pageList
        ];

        return view('signups.index')->with($model);
           
    }

    function  getSignupSummary($signups)
    {
      
        return [
            'total' => $signups->count(),
            'ok' => $signups->where('status', 1 )->count(),
            'no' => $signups->where('status', 0 )->count(),
            'canceled' =>  $signups->where('status', -1 )->count()
        ];

    }

    //Ajax
    function fetchSignups(int $term = 0, int $center = 0, int $course = 0, int $status = 0, int $page=1 ,int $pageSize=999)
    {
       
        $selectedCenter = null;
        $selectedTerm = null;
        $selectedCourse = null;

        if ($course)
        {
         
            $selectedCourse = $this->courses->getById($course);
            
            if (!$selectedCourse) abort(404);
            else
            {
                $selectedCenter = $selectedCourse->center;
                $selectedTerm = $selectedCourse->term;
            }

        }
        else
        {
            if ($center)
            {
                $selectedCenter = $this->centers->getById($center);
                if (!$selectedCenter) abort(404);
            }


            $selectedTerm =  $this->terms->getById($term);
            if (!$selectedTerm) return abort(404);

        }

        if (!$selectedCourse)
        {
            $course = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        $signups = $this->signups->fetchSignups($selectedTerm, $selectedCenter, $selectedCourse);


        $summary=$this->getSignupSummary($signups);

        $signups = $signups->where('status' , $status);


        $pageList = new PagedList($signups,$page,$pageSize);


        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'courseOptions' => $courseOptions,
            'summaryModel' => $summary,
            'model' => $pageList
        ];

        return response() ->json($model);

        
    }
    

    public function create()
    {
        $request=request();

        $course=0;
        if($request->course)  $course=(int)$request->course;
        if(!$course) abort(404);

        $selectedCourse=$this->courses->getById($course);
        if(!$selectedCourse) abort(404);

        $signup=Signup::init();

        $signupDetails=SignupDetail::init($selectedCourse);

        $selectedCourse->fullName();
        $selectedCourse->loadClassTimes();
        $signupDetails['course'] =$selectedCourse;

        $signup['details']=[$signupDetails];
        $form=[
            'signup' => $signup,
            'user' => User::init(),
            'courseOptions' => $this->courses->options($selectedCourse->term,$selectedCourse->center),
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),
            'courseIds' => [$selectedCourse->id],
            'identityIds' => [],
            'lotus' => false
        ];

        return response() ->json($form);
      
    }

    public function store(SignupRequest $request)
    {
        $userId=$request->getUserId();
        // if(!$userId){
        //     //New User
        //     $userValues=$request->getUserValues();
         

        //     $needFullname=true;
        //     $needSID=true;
        //     $needDOB=true;
        //     $errors=$this->users->validateUserInputs($userValues,$needSID,$needDOB);

        //     if($errors) return $this->requestError($errors);

            
          
        //     $profileValues= $userValues['profile'];
           
        //     $userValues=$request->getClearUserValues();

        //     $user=$this->users->createUser(
        //         new User($userValues),
        //         new Profile($profileValues)
        //     );

        //     $userId=$user->id;


        // }

        $signupValues=$request->getSignupValues();

        dd($signupValues);

        $courseIds=$request['courseIds'];
        if(!count($courseIds))
        {
            $errors['courseIds'] = ['請選擇報名課程'];
            return $this->requestError($errors);
        }

        $user = null;
        
        dd($userId);

        // if (String.IsNullOrEmpty(model.user.id))
        // {
        //     user = model.user.MapToEntity(CurrentUserId);
        //     var profile = model.user.profile.MapToEntity(CurrentUserId);
        //     user = await userService.CreateUserAsync(user, profile);
        // }
        // else
        // {
        //     user = userService.GetUserById(model.user.id);
        //     if (user == null) throw new Exception(String.Format("Action:SignupsController.Store , Error:GetUserById={0} 不存在", model.user.id));

        //     user = model.user.MapToEntity(CurrentUserId, user);
        //     user.Profile = model.user.profile.MapToEntity(CurrentUserId, user.Profile);

        //     await userService.UpdateUserAsync(user);
        // }




    



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

        $signupValues['updatedBy']=$updatedBy;
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
            'account' => $signupValues['account'],
            'money' => $signupValues['wage'],
            'updatedBy' => $updatedBy,
        ];

        $signup=Signup::find($userId);
        if($signup){
            $signup->update($signupValues);
            $signup->addRole();

            $wage=$signup->getWage();
            if($wage) $wage->update($wageValues);

        }else{
            $signup=$this->signups->createSignup($user,new Signup($signupValues), $wageValues);
            $signup->userId=$userId;
        }

        $signup->centers()->sync($centerIds);
       
        return response() ->json($signup);
    }

    public function show($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        $signup->fullName();
        $signup->loadClassTimes();
        $this->setTeachers($signup);
        $this->setCategories($signup);

        $signup->processes;

        $signup->canEdit = $this->canEdit($signup);
        $signup->canReview =$this->canReview($signup);
        $signup->canDelete = $this->canDelete($signup);

        return response() ->json($signup);
        
    }

    public function edit($id)
    {
       
        $signup = $this->signups->getById($id); 
        if(!$signup) abort(404);   
        if(!$this->canEdit($signup)) $this->unauthorized();

        $signup->number=$signup->serial();
        $this->setCategories($signup);
       
        $teacherIds=$this->getTeacherIds($signup);

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $withEmpty=false;
        $categoryOptions = $this->categories->options($withEmpty);
        
        $center=$signup->center;
       
        $teacherOptions = array_merge($this->teachers->options($center), $this->teacherGroups->options($center));
       
        $form=[
            'signup' => $signup,
            'teacherIds' =>$teacherIds,
            'teacherOptions' => $teacherOptions,
            'termOptions' => $this->terms->options(),
            'centerOptions' => $centerOptions,
            'categoryOptions' => $categoryOptions
        
        ];

        return response() ->json($form);
       
        
    }


    public function update(SignupRequest $request, $id)
    {
        $signup = $this->signups->getById($id); 
        if(!$signup) abort(404);   
        if(!$this->canEdit($signup)) $this->unauthorized();

        $signupValues=$request->getSignupValues();
        $teacherIdValues=$request->getTeacherIds();
       
        $errors=$this->validateSignupInputs($signupValues,$teacherIdValues);
        
        $category =Category::find($signupValues['categoryId']);
        $center = Center::find($signupValues['centerId']);
        $term = Term::find($signupValues['termId']);

        $signupValues['number'] =Signup::initSignupNumber($signupValues['number'] ,$category,$center ,$term );
        $duplicate = $this->signups->getSignupByNumber($signupValues['number']);
        if ($duplicate && $duplicate->id != $id)
        {
            $errors['signup.number'] = ['編號重複了'];
        }
       

        if($errors) return $this->requestError($errors);


        $teacherIds = [];
        foreach($teacherIdValues as $item){
            if (Helper::isTrue($item['group']))
            {
                $signupValues['teacherGroupId'] = $item['value'];
            }
            else
            {
                array_push($teacherIds,$item['value']);
            }
        }

        $signupValues['updatedBy']=$this->currentUserId();
        $signup->fill($signupValues);

        $this->signups->updateSignup($signup,[$category->id], $teacherIds);

        return response() ->json();
    }

    

    public function destroy($id) 
    {
        $signup = Signup::findOrFail($id);
        if(!$this->canDelete($signup)) $this->unauthorized();

        $this->signups->deleteSignup($signup, $this->currentUserId());
       
       
        return response() ->json();
    }

 


    
}
