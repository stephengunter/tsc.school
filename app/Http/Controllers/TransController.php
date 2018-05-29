<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TranRequest;
use App\Http\Requests\QuitRequest;

use App\Term;
use App\Center;
use App\Course;
use App\Student;
use App\Tran;
use App\Quit;
use App\Payway;
use App\Signup;
use App\SignupDetail;
use App\QuitDetail;


use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;
use App\Services\Signups;
use App\Services\Students;
use App\Services\Trans;
use App\Services\Payways;
use App\Services\Quits;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;
use DB;

class TransController extends Controller
{
    
    public function __construct(Terms $terms,Trans $trans,Students $students,Courses $courses,
        Signups $signups,Centers $centers,Payways $payways,Quits $quits)             
    {
        $this->terms=$terms;
        $this->trans=$trans;
        $this->students=$students;
        $this->courses=$courses;
        $this->signups=$signups;
        $this->centers=$centers;
        $this->payways=$payways;
        $this->quits=$quits;
    }

    function canEditStudent($student)
    {
        if($student->hasQuit()) return false;
        return $this->canAdminCenter($student->getCenter());
    }

    function canDelete($tran)
    {
        if(!$tran->canDelete()) return false;
        return $this->canAdminCenter($tran->getCenter());
    }
    function canQuitSignup(Signup $signup)
    {
        if($signup->status < 1) return false;

        return $this->canAdminCenter($signup->getCenter());

    }

    function readIndexRequest()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

      
        $term=0;
        if($request->term)  $term=(int)$request->term;

        $key='';
        if($request->key)  $key = $request->key;

        $keys=['west','east'];
        if(!$key) $key=$keys[0];
        else{
            if(!in_array($key,$keys)) abort(404);
        }
        
        
        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;


        $selectedTerm = null;
        if($term) $selectedTerm=Term::findOrFail($term);
        else $selectedTerm=$this->terms->getActiveTerm();
        

        $termOptions = []; 
        $keyOptions = []; 
        if(!$this->isAjaxRequest()){
            $termOptions = $this->terms->options();
            $keyOptions =$this->centers->getKeyOptions();
        }  

       

        $params=[
            'term' => $selectedTerm->id,
            'key' => $key,
            'keyword' => $keyword,
            'page' => $page,
            'pageSize' => $pageSize

        ];

       
        return [
            'selectedTerm' => $selectedTerm,

            'params' => $params,

            'termOptions' => $termOptions,
            'keyOptions' => $keyOptions,
        ];
    }


    public function index()
    {
        $requestValues=$this->readIndexRequest();

        $params=$requestValues['params'];

        $selectedTerm=$requestValues['selectedTerm'];
        $key=$params['key'];
        $keyword=$params['keyword'];

        $trans = $this->trans->fetchTrans($key,$selectedTerm->id,$keyword);

        $pageList =$this->getPageList($trans, $params['page'],$params['pageSize']);
        
        if($this->isAjaxRequest()){
           
            $model=[
                'model' => $pageList,
            ];
    
            return response()->json($model);
        }

        $termOptions=$requestValues['termOptions'];
        $keyOptions=$requestValues['keyOptions'];

        $model=[
            'title' => '轉班紀錄',
            'menus' => $this->adminMenus('StudentsAdmin'),

            'list' => $pageList,
            
            'terms' => $termOptions,               
            'keys' => $keyOptions,

            'params' => $params
        ];

        return view('trans.index')->with($model);


        
    }
   
    function getPageList($trans,$page,$pageSize)
    {
        $trans = $trans->orderBy('date','desc');
        $pageList = new PagedList($trans,$page,$pageSize);
        
        foreach($pageList->viewList as $tran){
            $tran->loadViewModel();
            $tran->canDelete=$this->canDelete($tran);
        }  

        return $pageList;
    }
    
    public function create()
    {
        
        $request=request();

        $student=0;
        if($request->student)  $student=(int)$request->student;
        if(!$student) abort(404);

        $selectedStudent=$this->students->getById($student);
        if(!$selectedStudent) abort(404);

        $center=$selectedStudent->course->center;
        $term=$selectedStudent->course->term;
        $centerId=$center->id;

        $centers=$this->centers->getCentersByKey($center->key)->get();
        $centerOptions=$this->centers->mapToOptions($centers);

        $courses=$this->getCoursesCanTran($center,$term,$selectedStudent);

      
        $tran=Tran::init();
        $tran['studentId']=$selectedStudent->id;

        $form=[
            'student' => $selectedStudent,
            'tran' => $tran,
            'centerOptions' => $centerOptions,
            'courses' => $courses,
            'centerId' => $center->id
          
        ];

        return response()->json($form);
      
    }

    public function fetchCourses()
    {
        $request=request();

        $student=0;
        if($request->student)  $student=(int)$request->student;
        if(!$student) abort(404);

        $selectedStudent=$this->students->getById($student);
        if(!$selectedStudent) abort(404);

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $selectedCenter = Center::findOrFail($center);

        $term = $this->terms->getActiveTerm();

        $courses=$this->getCoursesCanTran($selectedCenter,$term,$selectedStudent);

        return response()->json($courses);

       
    }

    function getCoursesCanTran($center,$term,$selectedStudent)
    {
        
        $userStudentCourseIds= $this->students->getByUserId($selectedStudent->userId)
                                    ->pluck('courseId')->toArray(); 
                                    
        $courses=$this->courses->fetchCourses($term->id,$center)
                                ->where('active',true)
                                ->whereNotIn('id',$userStudentCourseIds)
                                ->get();
        foreach($courses as $course){
            $course->fullName();
            $course->loadClassTimes();
        }                                 
        return $courses;                                
                                         
    }

    public function store(TranRequest $request)
    {
        $updatedBy=$this->currentUserId();
        $studentId=$request->getStudentId();
        
        $tranValues=$request->getTranValues();

        $student=$this->students->getById($studentId);
        
        if(!$this->canEditStudent($student)) return $this->unauthorized();


        $tranValues['updatedBy']= $this->currentUserId();


        $courseId=$tranValues['courseId'];
        $newCourse=$this->courses->getById($courseId);

        $errors=[];
        $existStudent=$this->students->findStudent($courseId, $student->userId);
        if($existStudent) $errors['courseId'] = ['此學員已經是此課程的學員'];
        

        $tran=$this->trans->createTran($student,$tranValues,$newCourse);
        
        return response()->json($tran);
       
    }

    public function createQuit()
    {
        $request=request();

        $tran=0;
        if($request->tran)  $tran=(int)$request->tran;

        $selectedTran=$this->trans->getById($tran);
        if(!$selectedTran) abort(404);

        $mustBackAmount = $selectedTran->getMustBackAmount();
        if(!$mustBackAmount) abort(404);

        $selectedSignup=$selectedTran->signupDetail->signup;

        $payway=$this->payways->defaultQuitPayway();

        $quit=Quit::init();
        $quit['tranId'] = $selectedTran->id;
        $quit['paywayId'] = $payway->id;
        $quit['signupId'] = $selectedSignup->id;
        $quit['tuitions'] = $mustBackAmount;

        if($payway->needAccount()){
            $quit= $this->quits->initQuitAccountValues($quit ,$selectedSignup->user);
        }

       
        $form=[
            'quit' => $quit,
            'payway' => $payway
        ];

        return response()->json($form);
        
    }

    public function storeQuit(QuitRequest $request)
    {
        $updatedBy=$this->currentUserId();
        $quitValues=$request->getQuitValues(); 

        $signup=$this->signups->getById($quitValues['signupId']);
        if(!$this->canQuitSignup($signup)) return $this->unauthorized();

        $selectedTran=$this->trans->getById($quitValues['tranId']);
        if(!$selectedTran) abort(404);

        $mustBackAmount = $selectedTran->getMustBackAmount();
        if(!$mustBackAmount) abort(404);

       
        $special=false;
        $auto=false;

        $payway=Payway::findOrFail($quitValues['paywayId']);      

        $errors=$this->quits->validateQuitInputs($quitValues,$payway);
       
        if($errors) return $this->requestError($errors);

        $quitDetail=new QuitDetail([
           
            'signupDetailId' => $selectedTran->signupDetail->id,
            'percents' => 0,
            'tuition' => $mustBackAmount,
            'updatedBy' => $updatedBy
        ]);

        $quitValues['updatedBy']=$updatedBy;

        $quit=new Quit($quitValues);

        $quit=$this->quits->createQuit($signup, $quit,[$quitDetail]);
        
        
        return response()->json();
    }
    
}
