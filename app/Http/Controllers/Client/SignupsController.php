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

  
    public function index()
    {
        $user=$this->currentUser();

        $signups = $this->signups->fetchSignupsByUser($user)
                                ->orderBy('created_at','desc');
                               
        $unPayedSignups=$signups->where('status',0)->get();
        $PayedSignups=$signups->where('status',1)->get();
        $canceledSignups=$signups->where('status',-1)->get();

        $signupRecords=Helper::mergeCollections($unPayedSignups,$PayedSignups);
        $signupRecords=Helper::mergeCollections($signupRecords,$canceledSignups);
        
      

        foreach($signupRecords as $signup){
            $signup->loadViewModel();
            $signup->canDelete=$this->canDelete($signup);
        }

        if($this->isAjaxRequest()){
            return response()->json($signupRecords);
        }
        
        $model=[
            'title' => '',
            'topMenus' => $this->clientMenus(),

            'signups' => $signupRecords
        ];

        return view('client.signups.index')->with($model);
    }

    public function create()
    {
        $request=request();

        $course=0;
        if($request->course)  $course=(int)$request->course;
        if(!$course) abort(404);

        $selectedCourse = $this->courses->getById($course);
        if(!$selectedCourse) abort(404);
       

        $signup=Signup::init();

        $signupDetails=SignupDetail::init($selectedCourse);

        $selectedCourse->fullName();
        $selectedCourse->loadClassTimes();
        $signupDetails['course'] =$selectedCourse;

        $signup['details']=[$signupDetails];

        $user = $this->users->getById($this->currentUserId());

        $model=[
            'title' => '線上報名 - ' .  $selectedCourse->fullName(),
            'topMenus' => $this->clientMenus(),

            'signup' => $signup,
            'user' => $user,
            'courseIds' => [$selectedCourse->id],
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),            
            'identityIds' => [],
            'lotus' => false
        ];

        return view('client.signups.edit')->with($model);

       

    }

    public function store(SignupRequest $request)
    {
        $user=$this->currentUser();
        $updatedBy=$user->id;

        $userValues=$request->getUserValues();
        $roleName=Role::studentRoleName();

        $errors=$this->users->validateUserInputs($userValues,$roleName);
        if($errors) return $this->requestError($errors);

        $profileValues= $userValues['profile'];
        $userValues=$request->getClearUserValues();
        $userValues['updatedBy'] =  $updatedBy;
        $profileValues['updatedBy'] =  $updatedBy;
    
        $user->profile->update($profileValues);
        $this->users->updateUser($user,$userValues);

        $courseIds=$request['courseIds'];

        $signupDetails=[];

        $selectedCourse=$this->courses->getById($courseIds[0]);

          //User報名過的課程記錄
        $coursesSignupedIds = [];
        $userSignupDetailRecords = $this->signups->getSignupDetailsByUser($user);
      
        $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
       
        if (in_array($selectedCourse->id, $coursesSignupedIds)){
            $errors['courseIds'] = ['您已經報名過此課程' ];
            return $this->requestError($errors);
        }

        $detail = new SignupDetail([
            'courseId' => $selectedCourse->id,
            'tuition' => $selectedCourse->tuition,
            'cost' => $selectedCourse->cost,
            'updatedBy' => $updatedBy

        ]);
        array_push($signupDetails,$detail);

        $signup=new Signup(['userId' => $user->id , 'points' => 0 , 'status' => 0 , 'net'=> true , 'updatedBy' => $updatedBy ]);

        $tuitions = 0;
        foreach($signupDetails as $signupDetail) {
            $tuitions += $signupDetail['tuition'];
        }
        
        $signup->tuitions = $tuitions;
        $signup->costs = 0;
        foreach($signupDetails as $signupDetail) {
            if($signupDetail->cost)  $signup->costs += $signupDetail->cost;
        }

        $signup=DB::transaction(function() use($signup,$signupDetails) {
            $signup->save();
            $signup->details()->saveMany($signupDetails);

            return $signup;
        });

     
    
        
        return response()->json($signup);
       
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
