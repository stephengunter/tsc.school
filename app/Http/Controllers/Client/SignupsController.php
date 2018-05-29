<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\User;
use App\Profile;
use App\Role;
use App\Term;
use App\Center;
use App\Course;
use App\Signup;
use App\SignupDetail;

use App\Services\Signups;
use App\Services\Bills;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;
use App\Http\Requests\SignupRequest;
use Carbon\Carbon;

use App\Core\PagedList;
use App\Core\Helper;
use DB;

class SignupsController extends Controller
{
    public function __construct(Signups $signups, Discounts $discounts, Bills $bills,
     Users $users,Terms $terms,Centers $centers,Courses $courses)        
    {
        $this->signups=$signups;
        $this->bills=$bills;
        $this->discounts=$discounts;
        $this->users=$users;
      
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
    }

    function canDelete($signup)
    {
        return $signup->status == 0 ;

    }
    function canEdit($signup)
    {
        return $signup->status == 0 ;

    }
    function canPay($signup)
    {
        return $signup->status == 0 ;

    }

    function canNotSignup(Course $course,User $user)
    {
        //User報名過的課程記錄
        $coursesSignupedIds = [];
        $userSignupDetailRecords = $this->signups->getSignupDetailsByUser($user);
        
        $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
        
        if (in_array($course->id, $coursesSignupedIds)){
            return '您已經報名過此課程' ;
        }

        return '';
    }


  
    public function index()
    {
        $user=$this->currentUser();

        $signups = $this->signups->fetchSignupsByUser($user)
                                ->orderBy('created_at','desc')
                                ->get();
                              
        if(count($signups)){
            $unPayedSignups=$signups->where('status',0);
           
            $payedSignups=$signups->where('status',1);

            $canceledSignups=$signups->where('status',-1);
            
            $signups=Helper::mergeCollections($unPayedSignups,$payedSignups);
            $signups=Helper::mergeCollections($signups,$canceledSignups);
        }                     
       
     
      

        foreach($signups as $signup){
            $signup->loadViewModel();
            $signup->canDelete=$this->canDelete($signup);
            $signup->canPay=$this->canPay($signup);
        }

        if($this->isAjaxRequest()){
            return response()->json($signups);
        }
        
        $model=[
            'title' => '',
            'topMenus' => $this->clientMenus(),
            'signups' => $signups
        ];

        return view('client.signups.index')->with($model);
    }

    public function create()
    {
       
        $user = $this->users->getById($this->currentUserId());

        $request=request();

        $course=0;
        if($request->course)  $course=(int)$request->course;
        if(!$course) abort(404);

        $selectedCourse = $this->courses->getById($course);
        if(!$selectedCourse) abort(404);

        $courseIds=[$selectedCourse->id];

        if($this->canNotSignup($selectedCourse, $user)){
            
            $model=[
                'title' => '線上報名 - ' .  $selectedCourse->fullName(),
                'topMenus' => $this->clientMenus(),
    
                'err' => 'canNotSignup'
            ];
    
            return view('client.signups.error')->with($model);
        }

        $signup=Signup::init();
       

        $signupDetail=SignupDetail::init($selectedCourse);

        $selectedCourse->fullName();
        $selectedCourse->loadClassTimes();
        $signupDetail['course'] =$selectedCourse;

        $signupDetails=[$signupDetail];
       

        $term=$selectedCourse->term;
        $center=$selectedCourse->center;

        $userCanAddDetailSignup=$this->signups->getUserCanAddDetailSignup($term, $center,$user);
        if($userCanAddDetailSignup){
            $signup['id'] = $userCanAddDetailSignup->id;
            foreach($userCanAddDetailSignup->details as $detail){
                $course=$detail->course;
                $course->fullName();
                $course->loadClassTimes();

                $detail= $detail->toArray();
                $detail['course'] =$selectedCourse;
                array_push($signupDetails,$detail);
               
            }
          
        }

        

        $signup['details']=$signupDetails;

        $identityIds=$user->identities()->pluck('identity_id')->toArray();

      
        $center->discounts;

        $model=[
            'title' => '線上報名 - ' .  $selectedCourse->fullName(),
            'topMenus' => $this->clientMenus(),

            'signup' => $signup,
            'user' => $user,
            'center' => $center,
            'birdDateText' => $selectedCourse->term->birdDateText(),
            'courseIds' => $courseIds,
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),            
            'identityIds' => $identityIds,
            'lotus' => false,

            'err' => ''
        ];

        return view('client.signups.edit')->with($model);

       

    }

    public function store(SignupRequest $request)
    {
       
        $user=$this->currentUser();
        $updatedBy=$user->id;
        $identityIds = $request['identityIds'];

        $errors = $this->updateUser($request,$user);
        if($errors) return $this->requestError($errors);

        $courseIds=$request['courseIds'];
        $selectedCourses=$this->courses->getByIds($courseIds)->get();
        $errors=$this->signups->checkSelectedCourses($selectedCourses);
      
        if($errors)  return $this->requestError($errors);
       
        $center = $selectedCourses[0]->center;
        $term = $selectedCourses[0]->term;
        $result=$this->signups->initSignupDetails($user,$selectedCourses,$updatedBy);
        
        $errors=$result['errors'];
        if($errors)  return $this->requestError($errors);
      
        $signupDetails=$result['signupDetails'];
        $lotus=Helper::isTrue($request['lotus']);

        $userCanAddDetailSignup=$this->signups->getUserCanAddDetailSignup($term, $center,$user);
       
        if($userCanAddDetailSignup){
            $userCanAddDetailSignup->identity_ids=join(',', $identityIds);
            $userCanAddDetailSignup->net=true;
            $userCanAddDetailSignup->updatedBy=$updatedBy;

            $this->signups->updateSignup($userCanAddDetailSignup,$signupDetails);
        }else{
            $signup=new Signup(Signup::init());
            $signup->userId=$user->id;
            $signup->identity_ids=join(',', $identityIds);
            $signup->net=true;
            $signup->updatedBy=$updatedBy;
           
            $signup=$this->signups->createSignup($signup,$signupDetails,$user,$lotus);
            
            return response()->json($signup);
        };
       
    }

    function updateUser(SignupRequest $request,User $user)
    {
        $userValues=$request->getUserValues();
        $roleName=Role::studentRoleName();

        $errors=$this->users->validateUserInputs($userValues,$roleName);
       
        if($errors) return $errors;


        $profileValues= $userValues['profile'];
        $userValues=$request->getClearUserValues();
        $userValues['updatedBy'] =  $user->id;
        $profileValues['updatedBy'] =  $user->id;
        
        $this->users->updateUser($user,$userValues,$profileValues);
    }

    public function show($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        $user = $this->users->getById($this->currentUserId());
        if($signup->userId!=$user->id)  abort(404);

        $signup->loadViewModel();

        $signup->bill->payDate = new Carbon($signup->bill->payDate);
        $signup->bill->payDate = $signup->bill->payDate->toDateString();

        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();
        }
        

        $signup->canEdit = $this->canEdit($signup);
        $signup->canDelete = $this->canDelete($signup);

        $selectedCourse=$signup->details->first()->course;

        $center=$signup->getCenter();
        foreach($center->discounts as $discount){
            $discount->loadViewModel();
        }

        if(!$signup->canEdit){
            $model=[
                'title' => '報名紀錄 - ' .  $selectedCourse->fullName(),
                'topMenus' => $this->clientMenus(),
    
                'signup' => $signup,
                'center' => $center
            ];

            return view('client.signups.show')->with($model);
        }

        $term=$signup->getTerm();
        $birdDateText=$term->birdDateText();
        

        $identityIds=$user->identities()->pluck('identity_id')->toArray();
        
        $model=[
            'title' => '線上報名 - ' .  $selectedCourse->fullName(),
            'topMenus' => $this->clientMenus(),

            'signup' => $signup,
            'user' => $user,
            'center' => $center,
            'birdDateText' => $birdDateText,
            
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),            
            'identityIds' =>  $identityIds,
            'lotus' => false
        ];

        return view('client.signups.edit')->with($model);

     
        
    }

    public function update($id,SignupRequest $request)
    {
        $signup=$this->signups->getById($id);
       
        if(!$signup) abort(404);
        if(!$this->canEdit($signup)) abort(404);

        $user=$this->currentUser();
        $updatedBy=$user->id;
        $identityIds = $request['identityIds'];

        $errors = $this->updateUser($request,$user);
        if($errors) return $this->requestError($errors);

        $courseIds=$request['courseIds'];
        $selectedCourses=$this->courses->getByIds($courseIds)->get();
        $errors=$this->signups->checkSelectedCourses($selectedCourses);
      
        if($errors)  return $this->requestError($errors);
       
        $center = $selectedCourses[0]->center;
        $term = $selectedCourses[0]->term;
        $result=$this->signups->initSignupDetails($user,$selectedCourses,$updatedBy);
        
        $errors=$result['errors'];
        if($errors)  return $this->requestError($errors);
      
        $signupDetails=$result['signupDetails'];
        $lotus=Helper::isTrue($request['lotus']);

        $signup->identity_ids=join(',', $identityIds);
        $signup->net=false;
        $signup->updatedBy=$updatedBy;

        $this->signups->updateSignup($signup,$signupDetails,$lotus);
       
    }

    

    public function destroy($id) 
    {
        $signup = Signup::findOrFail($id);
        if(!$this->canDelete($signup)) abort(500);

        $this->signups->deleteSignup($signup, $this->currentUserId());
       
       
        return response()->json();
    }

}
