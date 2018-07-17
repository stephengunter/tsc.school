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
use App\Category;
use App\Center;
use App\Course;
use App\Signup;
use App\SignupDetail;
use App\Payway;

use App\Services\Signups;
use App\Services\Bills;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;
use App\Services\Payways;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class SignupsController extends Controller
{
    
    public function __construct(Signups $signups, Discounts $discounts, Bills $bills,
        Payways $payways,Users $users,Terms $terms,Centers $centers,Courses $courses)        
    {
        $this->signups=$signups;
        $this->bills=$bills;
        $this->discounts=$discounts;
        $this->users=$users;
      
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;

        $this->payways=$payways;
    }

    
    function canEditCenter($center)
    {
        
        if($this->currentUserIsDev()) return true;
       
        return $this->canAdminCenter($center);
    }

    function canDelete($signup)
    {
        if(!$signup->canDelete()) return false;

        if($this->currentUserIsDev()) return true;

        return $this->canAdminCenter($signup->getCenter());

    }
    
    function canImport()
    {
        return $this->currentUserIsDev();
    }
    function canQuit($signup)
    {
        if(!$signup->canQuit()) return false;

        return $this->canEditCenter($signup->getCenter());

    }
    function canReviewCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canAdminCenter($center);
    }

    function filterSignupsByKeyword($signups, $keyword)
    {
        $userIds=$this->users->getByKeyword($keyword)->pluck('id')->toArray();
        return $signups->whereIn('userId',$userIds);
    }

    public function seed()
    {
      
        if(!$this->currentUserIsDev()) dd('權限不足');

        $users=User::all();
        $index = 0;
        $active = true;
        

        $term = $this->terms->getActiveTerm();
        $reviewed=true;
        $selectedCenter=null;
        $selectedCategory=null;
        
        $courses=$this->courses->fetchCourses($term->id,$selectedCenter,$selectedCategory,$reviewed)->get();

        $updatedBy=$this->currentUserId();
       
        $userList = \App\User::all();
        
        for ($i = 0; $i < 50; $i++)
        {
            $user = $userList[$i];

            $exist=Signup::where('userId',$user->id)->first();
            if($exist) continue;

            $signup = new Signup();

            $countCount = ($i % 2) == 0 ? 1 : 2;
            $selectedCourses =[];

            array_push($selectedCourses , $courses->random());

            $selectedCenter=$selectedCourses[0]->center;
            
            if ($countCount > 1)
            {
                $firstCourse = $selectedCourses[0];
                
                $coursesCanSelect = $courses->where('id','!=', $firstCourse->id);
                $coursesCanSelect = $coursesCanSelect->filter(function ($course) use($selectedCenter) {
                    return $course->center->key==$selectedCenter->key;
                })->all();   
                
                if(count($coursesCanSelect)){
                    array_push($selectedCourses , array_random($coursesCanSelect) ); 
                }

            }

            $identityOptions=$this->discounts->getIdentitiesOptions($selectedCenter);
          
            $identity = array_random($identityOptions);
            $identityIds=[$identity['value']];

            $signupDetails=[];
    
            foreach ($selectedCourses as $selectedCourse)
            {
               
                $detail = new SignupDetail([
                    'courseId' => $selectedCourse->id,
                    'tuition' => $selectedCourse->tuition,
                    'cost' => $selectedCourse->cost ? $selectedCourse->cost : 0,
                    'updatedBy' => $updatedBy

                ]);
                array_push($signupDetails,$detail);
            }

            

            $lotus = ($i % 5) == 0 ? true : false;
            $net = ($i % 2) == 0 ? true : false;

            $userCanAddDetailSignup=$this->signups->getUserCanAddDetailSignup($term, $selectedCenter,$user);
           
            if($userCanAddDetailSignup){
                $userCanAddDetailSignup->identity_ids=join(',', $identityIds);
                $userCanAddDetailSignup->net=$net;
                $userCanAddDetailSignup->updatedBy=$updatedBy;

                $this->signups->updateSignup($userCanAddDetailSignup,$signupDetails);
            }else{
                $signup=new Signup(Signup::init());
                $signup->userId=$user->id;
                $signup->identity_ids=join(',', $identityIds);
                $signup->net=$net;
                $signup->updatedBy=$updatedBy;
            
                $signup=$this->signups->createSignup($signup,$signupDetails,$user,$lotus);
                
               
            };
          
         


        }//end for

        dd('done');

          
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

        $err_msg=$this->signups->importSignups($file,$this->currentUserId());
     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }

    function readIndexRequest()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $course=0;
        if($request->course)  $course=(int)$request->course;
       

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $status=0;
        if($request->status)  $status=(int)$request->status;

        
        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        $selectedTerm = null;
        $selectedCourse = null;
        if ($course)
        {
            $selectedCourse = $this->courses->getById($course);
            if (!$selectedCourse) abort(404);
        }
        else
        {
            if ($center) $selectedCenter = $this->centers->getById($center);

            if(!$this->isAjaxRequest() && !$selectedCenter){
                $selectedCenter=$this->defaultAdminCenter();
            }
            
            if ($term) $selectedTerm = $this->terms->getById($term);

        }

        if ($selectedCourse)  $pageSize = 999;
        else 
        {
            if ($pageSize == 999) $pageSize = 10;
          
        }

        $params=[
            'term' => $selectedTerm,
            'center' => $selectedCenter,
            'course' => $selectedCourse,

            'keyword' => $keyword,
            'status' => $status,

            'page' => $page,
            'pageSize' => $pageSize

        ];

        return $params;
        
    }
   
    public function index()
    {
        $params=$requestValues=$this->readIndexRequest();

        $selectedCourse=$requestValues['course'];
        if($selectedCourse){
            $selectedCenter = $selectedCourse->center;
            $selectedTerm = $selectedCourse->term;
        }else{
            $selectedCenter=$params['center'];

            $selectedTerm=$params['term'];
            if(!$selectedTerm){
                $selectedTerm=$this->terms->getActiveTerm();
            }
        }

        $status = $params['status'];
        $keyword= $params['keyword'];

        $signups = $this->signups->fetchSignups($selectedTerm, $selectedCenter, $selectedCourse);
       
        $signups = $signups->where('status' , $status);
       

        if($keyword){
            $signups =$this->filterSignupsByKeyword($signups, $keyword);
        }

        $page= $params['page'];
        $pageSize= $params['pageSize'];

        $canQuit=false;
        if($status==1){
           
            if($selectedCenter) $canQuit=$this->canEditCenter($selectedCenter);
        }

       
       
        $pageList =$this->getPageList($signups,$canQuit,$page,$pageSize);


        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $summary=$this->signups->getSignupSummary($selectedTerm, $selectedCenter, $selectedCourse);
        
        
        if($this->isAjaxRequest()){
            $model=[
                'courseOptions' => $courseOptions,
                'summaryModel' => $summary,
                'model' => $pageList,
                'canQuit' => $canQuit
            ];
    
            return response()->json($model);
        }

        $centerOptions = $this->centers->options();
        $termOptions = $this->terms->options();

        $counterPayways=$this->payways->counterPayways();
        $counterPaywayOptions=$counterPayways->map(function ($payway) {
            return $payway->toOption();
        })->all();
     
        $model=[
            'title' => '報名管理',
            'menus' => $this->adminMenus('SignupsAdmin'),
            'params' => $params,
            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,                
            'statuses' => $this->signups->statusOptions(),

            'canQuit' => $canQuit,
            'canImport' => $this->canImport(),
            'counterPayways' => $counterPaywayOptions,

            'summary' => $summary,
            'list' => $pageList
        ];

        return view('signups.index')->with($model);
           
    }

    
    function getPageList($signups,$canQuit,$page,$pageSize)
    {
        $pageList = new PagedList($signups,$page,$pageSize);
      
        foreach($pageList->viewList as $signup){
            $signup->loadViewModel();
          
            if($canQuit)  $signup->canQuit=$this->canQuit($signup);
            
        }  

        return $pageList;
    }

    

    public function create()
    {
        $request=request();

        $course=0;
        if($request->course)  $course=(int)$request->course;
        $selectedCourse=null;
        if($course) $selectedCourse=$this->courses->getById($course);


        $selectedCenter=null;
        $term=null;
        if($selectedCourse){
            $selectedCenter=$selectedCourse->center;
            $term=$selectedCourse->term;
        }else{
            $center=0;
            if($request->center)  $center=(int)$request->center;
        
            if($center) $selectedCenter=$this->centers->getById($center);
            if(!$selectedCenter) abort(404);

            $term = $this->terms->getActiveTerm();
        }
       
        $signup=Signup::init();
        $courseIds=[];
        
        if($selectedCourse){
            $signupDetail=SignupDetail::init($selectedCourse);

            $selectedCourse->fullName();
            $selectedCourse->loadClassTimes();
            $signupDetail['course'] =$selectedCourse;

            $signup['details']=[$signupDetail];

            $courseIds = [$selectedCourse->id];
        }else{

        }

        $centers=$this->centers->getCentersByKey($selectedCenter->key)->get();
       
        $centerOptions=$this->centers->mapToOptions($centers);

        $identityOptions=$this->discounts->getIdentitiesOptions($selectedCenter);
        
        $form=[
            'signup' => $signup,
            'user' => User::init(),
            'courseOptions' => [],
            'identityOptions' => $identityOptions,
            'courseIds' => $courseIds,
            'identityIds' => [],
            'lotus' => false,
            'centerId' => $selectedCenter->id,
            'centerOptions' => $centerOptions
        ];

        return response() ->json($form);
      
    }

    public function fetchCourses()
    {
        
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $selectedCenter = Center::find($center);

        $term = $this->terms->getActiveTerm();
        $reviewed=true;
        $selectedCategory=null;
        
        $courses=$this->courses->fetchCourses($term->id,$selectedCenter,$selectedCategory,$reviewed, $keyword)->get();

        $courses = $courses->filter(function ($course) {
            $net=false;
            return $course->canSignup($net);   
        })->values();

        foreach($courses as $course){
            $course->fullName();
            $course->loadClassTimes();
        } 

        
        return response() ->json($courses);

        
    }

    function createUser(SignupRequest $request)
    {
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

        return $user;
    }

    

    public function store(SignupRequest $request)
    {
      
        $updatedBy=$this->currentUserId();

        $courseIds=$request['courseIds'];
        if(!count($courseIds))
        {
            $errors['courseIds'] = ['請選擇報名課程'];
            return $this->requestError($errors);
        }

        $userId=$request->getUserId();
        if(!$userId){
            //New User
            $user=$this->createUser($request);

            $userId=$user->id;

        }

        $user=User::find($userId);
        $identityIds = $request['identityIds'];

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
            $userCanAddDetailSignup->net=false;
            $userCanAddDetailSignup->updatedBy=$updatedBy;

            $this->signups->updateSignup($userCanAddDetailSignup,$signupDetails,$lotus);
        }else{
            $signup=new Signup(Signup::init());
            $signup->date=$request->getSignupDate();
            $signup->userId=$userId;
            $signup->identity_ids=join(',', $identityIds);
            $signup->net=false;
            $signup->updatedBy=$updatedBy;
           
            $signup=$this->signups->createSignup($signup,$signupDetails,$user,$lotus);
            
            return response()->json($signup);
        };


       
       
    }

    


    public function show($id)
    {
        $signup = $this->signups->getById($id);
      
        if(!$signup) abort(404);

        $signup->loadViewModel();

        $signup->canEdit = $this->canEditCenter($signup->getCenter());
        $signup->canDelete = $this->canDelete($signup);
        $signup->canQuit = $this->canQuit($signup);

        foreach($signup->details as $signupDetail){
            
        }

        $canEditCenter=$this->canEditCenter($signup->getCenter());
        foreach($signup->quits as $quit){
            if($canEditCenter)  $quit->canEdit=$quit->canEdit();
            else   $quit->canEdit=false;
          
        }

        return response()->json($signup);
        
    }

    public function updatePS(Request $form)
    {
        $id=$form['id'];
        $ps=$form['ps'];

        $signup = $this->signups->getById($id);   
        if(!$signup) abort(404);   

        $canEdit=$this->canEditCenter($signup->getCenter());

        if(!$canEdit) return $this->unauthorized();
        
        
        $signup->update([
             'ps' => $ps,
             'updatedBy' => $this->currentUserId()
        ]);
        

        return response() ->json();


    }

    public function printBill($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        if($signup->payed) about(404);

        $this->bills->createBillCode($signup);

        return response()->json();

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
        
        $model['canReview'] = $this->canReviewCenter($selectedCenter);

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
        if(!$this->canDelete($signup)) return $this->unauthorized();

        $this->signups->deleteSignup($signup, $this->currentUserId());
       
       
        return response() ->json();
    }

 


    
}
