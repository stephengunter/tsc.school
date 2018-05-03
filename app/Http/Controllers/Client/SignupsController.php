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

        if($this->canNotSignup($selectedCourse, $user)){
            abort(404);
        }
       

        $userSignupRecords = $this->signups->fetchSignupsByUser($user)
                                ->orderBy('created_at','desc');
                               
        $unPayedSignups=$userSignupRecords->where('status',0)->get();

        $signupDetails=[];
        $signupId=0;
        foreach($unPayedSignups as $record){
            foreach($record->details as $detail){
                if($detail->course->termId==$selectedCourse->termId && $detail->course->centerId==$selectedCourse->centerId){
                    array_push($signupDetails,$detail);
                    if(!$signupId) $signupId=$detail->signupId;
                }
            }
        }

        foreach($signupDetails as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();
        }
        
        

        $signup=Signup::init();
        if($signupId) $signup['id']=$signupId;
        array_push($signupDetails,SignupDetail::init($selectedCourse));
       

        $signup['details']=$signupDetails;

        $identityIds=$user->identities()->pluck('identity_id')->toArray();

        $model=[
            'title' => '線上報名 - ' .  $selectedCourse->fullName(),
            'topMenus' => $this->clientMenus(),

            'signup' => $signup,
            'user' => $user,
          
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),            
            'identityIds' => $identityIds,
            'lotus' => false
        ];

        return view('client.signups.edit')->with($model);

       

    }

    public function store(SignupRequest $request)
    {
        
        $user=$this->currentUser();

        $errors = $this->updateUser($request,$user);
        if($errors) return $this->requestError($errors);

        $signupDetails=[];

        $postedSignupDetails=$request->getSignupDetails();
        foreach($postedSignupDetails as $detail){
              //新資料
            $selectedCourse=$this->courses->getById($detail['courseId']);
            $error=$this->canNotSignup($selectedCourse,$user);
            if($error){
                $errors['courseIds'] = [$error];
                return $this->requestError($errors);
            }

            array_push($signupDetails,new SignupDetail([
                
                'courseId' => $selectedCourse->id,
                'tuition' => $selectedCourse->tuition,
                'cost' => $selectedCourse->cost,
                'updatedBy' => $user->id
    
            ]));
        }

        $signup=new Signup(['userId' => $user->id , 'points' => 0 , 
                            'status' => 0 , 'net'=> true , 
                            'updatedBy' => $user->id ]
                        );

        $identityIds = $request['identityIds'];
       
        $this->setSignupMoney($signup, $signupDetails ,collect([$selectedCourse]), $identityIds);

       
        $signup=$this->signups->createSignup($signup,$signupDetails);
    
        if(count($identityIds)){
            $this->users->addIdentitiesToUser($user,$identityIds);
        }


        return response()->json($signup);
       
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
        
    
        $user->profile->update($profileValues);
        $this->users->updateUser($user,$userValues);
    }

    public function show($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        $user = $this->users->getById($this->currentUserId());
        if($signup->userId!=$user->id)  abort(404);

       

        $signup->loadViewModel();

        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();
        }
        

        $signup->canEdit = $this->canEdit($signup);
        $signup->canDelete = $this->canDelete($signup);

        $selectedCourse=$signup->details->first()->course;

        if(!$signup->canEdit){
            $model=[
                'title' => '報名紀錄 - ' .  $selectedCourse->fullName(),
                'topMenus' => $this->clientMenus(),
    
                'signup' => $signup
            ];

            return view('client.signups.show')->with($model);
        }

        

        $identityIds=$user->identities()->pluck('identity_id')->toArray();
        
        $model=[
            'title' => '線上報名 - ' .  $selectedCourse->fullName(),
            'topMenus' => $this->clientMenus(),

            'signup' => $signup,
            'user' => $user,
            
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

        $errors = $this->updateUser($request,$user);
        if($errors) return $this->requestError($errors);

        $signupDetails=[];
        $selectedCourses=[];
        $postedSignupDetails=$request->getSignupDetails();
        foreach($postedSignupDetails as $detail){
            $detailId=0;        
            if(array_key_exists ( 'id' ,$detail)) $detailId=(int)$detail['id'];
            
            if($detailId){
                array_push($signupDetails,SignupDetail::findOrFail($detailId));
                array_push($selectedCourses,$this->courses->getById($detail['courseId']));
            }else{
                 //新資料
                $selectedCourse=$this->courses->getById($detail['courseId']);
                array_push($selectedCourses,$selectedCourse);

                $error=$this->canNotSignup($selectedCourse,$user);
                if($error){
                    $errors['courseIds'] = [$error];
                    return $this->requestError($errors);
                }

                array_push($signupDetails,new SignupDetail([
                   
                    'courseId' => $selectedCourse->id,
                    'tuition' => $selectedCourse->tuition,
                    'cost' => $selectedCourse->cost,
                    'updatedBy' => $user->id
        
                ]));
               

            }
        }
       

        $identityIds = $request['identityIds'];
       
        $this->setSignupMoney($signup, $signupDetails ,collect($selectedCourses), $identityIds);

        
        $this->signups->updateSignup( $signup,  $signupDetails);
        
        return response()->json();
       
    }

    function setSignupMoney(Signup $signup, array $signupDetails ,$courses, $identityIds=[], $lotus=false)
    {
       
        $terms= $courses->pluck('termId')->all();
        //課程必須在同一學期
        if(count(array_unique($terms)) > 1) abort(500);
        //課程必須在同一中心
        $centers= $courses->pluck('centerId')->all();
        if(count(array_unique($centers)) > 1) abort(500);
        
     
        $center = $courses[0]->center;
        $term = $courses[0]->term;

        $bestDiscount=$this->discounts->findBestDiscount($center,$term,$identityIds,$lotus, count($courses));
       
        
        if($term->canBird(Carbon::today())){
        
           
            $signup->points = $bestDiscount->pointOne;

            if ($signup->points < 100)
            {
                $signup->discount = $bestDiscount->name;
            }

            if ($bestDiscount->bird())
            {
                $signup->discount .= " - 早鳥優惠";
            }

        }else{
        
            if ($signup->points < 100)
            {
                $signup->discount = $bestDiscount->name;
            }

            $signup->points = $bestDiscount->pointTwo;
        }

        $tuitions = 0;
        foreach($signupDetails as $signupDetail) {
            $tuitions += $signupDetail['tuition'];
        }
        
        $signup->tuitions = $tuitions * $signup->points / 100;
        $signup->costs = 0;
        foreach($signupDetails as $signupDetail) {
            if($signupDetail->cost)  $signup->costs += $signupDetail->cost;
        }

    }

    public function destroy($id) 
    {
        $signup = Signup::findOrFail($id);
        if(!$this->canDelete($signup)) abort(500);

        $this->signups->deleteSignup($signup, $this->currentUserId());
       
       
        return response()->json();
    }

}
