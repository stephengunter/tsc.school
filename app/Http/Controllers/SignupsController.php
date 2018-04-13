<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\SignupInfoRequest;

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

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

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

    function canEdit($signup)
    {
        if($this->currentUserIsDev()) return true;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$signup->getCenter()]);

        if(count($intersect)) return true;
        return false;

    }
    function canDelete($signup)
    {
        if($signup->status>0) return false;
        return $this->canEdit($signup);

    }
    function canReview(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$course->center]);

        
        if(count($intersect)) return true;
        return false;
    }

    public function seed()
    {
        if(!$this->currentUserIsDev()) dd('權限不足');

        $users=User::all();
        $index = 0;
        $active = true;
        $term=\App\Term::where('active',true)->first();
        $center=\App\Center::where('head',true)->first();
        $courses= $this->courses->fetchCourses($term->id,$center)->get();

        $updatedBy=$this->currentUserId();
       
        $userList = \App\User::all();
        for ($i = 0; $i < 50; $i++)
        {
            $user = $userList[$i];
            $signup = new Signup();

            $countCount = ($i % 2) == 0 ? 1 : 2;
            $selectedCourses =[];
            
            
            array_push($selectedCourses , $courses->random());
            
            if ($countCount > 1)
            {
                $firstCourse = $selectedCourses[0];
                
                $coursesCanSelect = $courses->where('id','!=', $firstCourse->id);
                
                if(count($coursesCanSelect)){
                    array_push($selectedCourses , $coursesCanSelect->random() ); 
                }

            }

            $identities = $this->discounts->getIdentitiesOptions($center);
            $identity = array_random($identities, 1)[0];
            $identityIds=[$identity['value']];

            $signupDetails=[];
    
            foreach ($selectedCourses as $selectedCourse)
            {
                $detail = new SignupDetail([
                    'courseId' => $selectedCourse->id,
                    'tuition' => $selectedCourse->tuition,
                    'cost' => $selectedCourse->cost,
                    'updatedBy' => $updatedBy

                ]);
                array_push($signupDetails,$detail);
            }

            $lotus = ($i % 5) == 0 ? true : false;
            $net = ($i % 2) == 0 ? true : false;

            $signup=new Signup(['userId' => $user->id , 'net'=> $net , 'updatedBy' => $updatedBy ]);

            $this->setSignupMoney($signup, $signupDetails ,collect($selectedCourses), $identityIds, $lotus);

            
            $signup=$this->signups->createSignup($signup,$signupDetails);


        }//end for

        dd('done');

          
    }

    public function seedBills()
    {
        if(!$this->currentUserIsDev()) dd('權限不足');

        $bills = \App\Bill::all();
        foreach ($bills as $bill)
        {
            $num = rand(0 ,100);
            if (($num % 2) == 0) continue;
            $code = $bill->code;
            $amount = $bill->amount;

            $num = rand(0 ,100);
            $payway = ($num % 2) == 0 ? 1 : 2;

            $this->bills->payBill($code, $amount, $payway);

            
        }
        

        dd('done');
        
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

        $status=0;
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
        $signups = $signups->where('status' , $status);
       
        $summary=$this->signups->getSignupSummary($selectedTerm, $selectedCenter, $selectedCourse);

        $pageList =$this->getPageList($signups,$page,$pageSize);


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

    
    function getPageList($signups,$page,$pageSize)
    {
        $pageList = new PagedList($signups,$page,$pageSize);
        foreach($pageList->viewList as $signup){
            $signup->loadViewModel();
        }  

        return $pageList;
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
        $signups = $signups->where('status' , $status);

        $summary=$this->signups->getSignupSummary($selectedTerm, $selectedCenter, $selectedCourse);
        
        $pageList =$this->getPageList($signups,$page,$pageSize);

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
       
        $updatedBy=$this->currentUserId();

        $userId=$request->getUserId();
        $identityIds = $request['identityIds'];
        if(!$userId){
            //New User
            $userValues=$request->getUserValues();
            
            $roleName=Role::studentRoleName();
            $errors=$this->users->validateUserInputs($userValues,$roleName);

            if($errors) return $this->requestError($errors);
          
            $profileValues= $userValues['profile'];
           
            $userValues=$request->getClearUserValues();

            $user=$this->users->createUser(
                new User($userValues),
                new Profile($profileValues)
            );

            $userId=$user->id;

        }

        $user=User::find($userId);

        $identityIds = $request['identityIds'];
        if(count($identityIds)){
           $this->users->addIdentitiesToUser($user,$identityIds);
        }
      

        $courseIds=$request['courseIds'];
        if(!count($courseIds))
        {
            $errors['courseIds'] = ['請選擇報名課程'];
            return $this->requestError($errors);
        }

        $signupDetails=[];

        $selectedCourses=$this->courses->getByIds($courseIds)->get();

          //User報名過的課程記錄
        $coursesSignupedIds = [];
        $userSignupDetailRecords = $this->signups->getSignupDetailsByUser($user);
        $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
        foreach ($selectedCourses as $selectedCourse)
        {

            if (in_array($selectedCourse->id, $coursesSignupedIds)){
                $errors['courseIds'] = ['此學員已經報名過課程' . $selectedCourse->fullName() ];
                return $this->requestError($errors);
            }

            $detail = new SignupDetail([
                'courseId' => $selectedCourse->id,
                'tuition' => $selectedCourse->tuition,
                'cost' => $selectedCourse->cost,
                'updatedBy' => $updatedBy

            ]);
            array_push($signupDetails,$detail);
        }

        $signup=new Signup(['userId' => $userId , 'net'=> false , 'updatedBy' => $updatedBy ]);

        $this->setSignupMoney($signup, $signupDetails ,$selectedCourses, $identityIds,  $request['lotus']);

       
        $signup=$this->signups->createSignup($signup,$signupDetails);
        
        return response() ->json($signup);
       
    }

    function setSignupMoney(Signup $signup, array $signupDetails ,$courses, $identityIds, $lotus)
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


    public function show($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        $signup->loadViewModel();

        $signup->canEdit = $this->canEdit($signup);
        $signup->canDelete = $this->canDelete($signup);

        

        return response() ->json($signup);
        
    }
   
    public function report()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);

        $selectedTerm = null;
        $selectedCenter = null;
        $model=[];

        if($this->isAjaxRequest()){
          
            $selectedTerm = $this->terms->getById($term);
            $selectedCenter =  $this->centers->getById($center);
        }else{
            $termOptions = $this->terms->options();
            $centerOptions = $this->centers->options();

            if ($center) $selectedCenter =  $this->centers->getById($center);
            else $selectedCenter = $this->centers->getById($centerOptions[0]['value']); 

            if ($term > 0) $selectedTerm = $this->terms->getById($term);
            else  $selectedTerm = $this->terms->getById($termOptions[0]['value']); 

           

            $model['terms'] = $termOptions;
            $model['centers'] = $centerOptions;

        }

        if(!$selectedTerm) abort(404);
        if(!$selectedCenter) abort(404);

        $model['canReview'] = $this->canReview($selectedCenter);

        $courses=$this->courses->fetchCourses( $selectedTerm->id ,$selectedCenter);
        $courses=$courses->where('active',$active);
           
        $pageList = new PagedList($courses);

        foreach($pageList->viewList as $course){
            $course->fullName();
            
        } 

        $signupReports=array_map(function($course){
            return [
                'course' => $course,
                'studentCount' => $course->activeStudent()->count()
            ];
        }, $pageList->viewList);
        
        $pageList->viewList=$signupReports;

        if($this->isAjaxRequest()){
          
            $model['model'] = $pageList;
            return response() ->json($model);
        }

        $model['title'] = '報名統計';
        $model['menus'] = $this->adminMenus('SignupsAdmin');
        $model['list'] = $pageList;

        return view('signups.report')->with($model);

       
    }

    public function destroy($id) 
    {
        $signup = Signup::findOrFail($id);
        if(!$this->canDelete($signup)) $this->unauthorized();

        $this->signups->deleteSignup($signup, $this->currentUserId());
       
       
        return response() ->json();
    }

 


    
}
