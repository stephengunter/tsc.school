<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TranRequest;

use App\Term;
use App\Center;
use App\Course;
use App\Student;
use App\Tran;
use App\SignupDetail;


use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;
use App\Services\Signups;
use App\Services\Students;
use App\Services\Trans;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;
use DB;

class TransController extends Controller
{
    
    public function __construct(Terms $terms,Trans $trans,Students $students,Courses $courses,
        Signups $signups,Centers $centers)             
    {
        $this->terms=$terms;
        $this->trans=$trans;
        $this->students=$students;
        $this->courses=$courses;
        $this->signups=$signups;
        $this->centers=$centers;
    }

    function canEdit($tran)
    {
        if($this->currentUserIsDev()) return true;
        return true;
       

    }


    public function index()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $key='';
        if($request->key)  $key = $request->key;

        $keys=['west','east'];
        if(!in_array($key,$keys)) $key=$keys[0];
        
        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

       
        $selectedTerm = null;
        if($term) $selectedTerm=Term::findOrFail($term);
        else $selectedTerm=$this->terms->getActiveTerm();

        $pageList = $this->fetchTrans($key,$selectedTerm->id);

        

        $model['title'] = '轉班紀錄';
        $model['list'] = $pageList;
        $model['menus'] = $this->adminMenus('StudentsAdmin');
        $model['terms'] = $this->terms->options();
        $model['keys'] = $this->centers->getKeyOptions();
        $model['params'] = [ 'term' => $selectedTerm->id , 'key'=>$key,  'keyword'=>$keyword ];

        
       
       
        return view('trans.index')->with($model);
    }
   
    public function fetchTrans($key,$termId)
    {
        $selectedCenterIds=$this->centers->getCentersByKey($key)
                                         ->pluck('id')->toArray();
        $courseIds=$this->courses->getByTerm($termId)
                                ->whereIn('centerId', $selectedCenterIds)
                                ->pluck('id')->toArray();


        $tranIds = Student::whereIn('courseId', $courseIds)
                            ->pluck('tran_id_from')->toArray(); 
                            
        $tranIds = array_filter($tranIds);
      
        $trans=$this->trans->getAll()->whereIn('id',$tranIds);
       
       
       

        $pageList = new PagedList($trans);
        foreach($pageList->viewList as $tran){
            $tran->loadViewModel();
            $tran->canEdit=$this->canEdit($tran);
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
      
        //$isPay=$request->isPay();
        $tranValues=$request->getTranValues();

        $student=$this->students->getById($studentId);
        
        if(!$this->canEdit($student)) return $this->unauthorized();

        // $tuition=floatval($tranValues['tuition']);
        // if(!$isPay) $tuition = 0 - $tuition ;
        $courseId=$tranValues['courseId'];
        $newCourse=$this->courses->getById($courseId);

        $errors=[];
        $existStudent=$this->students->findStudent($courseId, $student->userId);
        if($existStudent) $errors['courseId'] = ['此學員已經是此課程的學員'];
        
        $signupDetail=$this->signups->getSignupDetailsByUser($student->user)
                                    ->where('courseId',$student->courseId)
                                    ->first();

        $signup=$this->updateSignup($newCourse,$signupDetail,$updatedBy);

        dd($signup);

        $tran=new Tran([
            'date' => $tranValues['date'],
            'signupDetailId' => $signupDetail->id,
            'courseId' => $tranValues['courseId'],
            'tuition' => $tuition,
            'ps' => $tranValues['ps'],
            'updatedBy' => $this->currentUserId(),

        ]);

        $tran=$this->trans->createTran($tran,$student);
        
        return response() ->json($tran);
       
    }

    function updateSignup($newCourse,$signupDetail,$updatedBy)
    {
        $newDetail = new SignupDetail([
            'courseId' => $newCourse->id,
            'tuition' => $newCourse->tuition,
            'cost' => $newCourse->cost,
            'updatedBy' => $updatedBy
        ]);

        $signup= DB::transaction(function() use($signupDetail,$newDetail,$updatedBy) {
            $signupDetail->tuition=0;
            $signupDetail->cost=0;
            $signupDetail->save();

            $signup=$signupDetail->signup;
            $signup->updatedBy=$updatedBy;
            $signup->details()->save($newDetail);
            $signup->updateMoney();
        });
            

        return $signup;
    }
 


    
}
