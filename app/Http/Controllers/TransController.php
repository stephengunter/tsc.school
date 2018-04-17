<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TranRequest;

use App\Term;
use App\Center;
use App\Course;
use App\Tran;
use App\SignupDetail;

use App\Services\Courses;
use App\Services\Signups;
use App\Services\Students;
use App\Services\Trans;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class TransController extends Controller
{
    
    public function __construct(Trans $trans,Students $students,Courses $courses,Signups $signups)             
    {
        $this->trans=$trans;
        $this->students=$students;
        $this->courses=$courses;
        $this->signups=$signups;
    }

    function canEdit($student)
    {
        if($this->currentUserIsDev()) return true;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$student->getCenter()]);

        if(count($intersect)) return true;
        return false;

    }

    function canTrans($student)
    {
        return $this->canEdit($student);

    }
   
   
    
    public function create()
    {
        
        $request=request();

        $student=0;
        if($request->student)  $student=(int)$request->student;
        if(!$student) abort(404);

        $selectedStudent=$this->students->getById($student);
        if(!$selectedStudent) abort(404);

        $course=$selectedStudent->course;
        $courseOptions=$this->courses->options($course->term,$course->center);

      
        $tran=Tran::init();
        $tran['studentId']=$selectedStudent->id;

        $form=[
            'student' => $selectedStudent,
            'tran' => $tran,
            'courseOptions' => $courseOptions
          
        ];

        return response() ->json($form);
      
    }

    public function store(TranRequest $request)
    {
       
        $studentId=$request->getStudentId();
        $isPay=$request->isPay();
        $tranValues=$request->getTranValues();

        $student=$this->students->getById($studentId);
        
        if(!$this->canEdit($student)) return $this->unauthorized();

        $tuition=floatval($tranValues['tuition']);
        if(!$isPay) $tuition = 0 - $tuition ;
        
        $signupDetail=$this->signups->getSignupDetailsByUser($student->user)
                                    ->where('courseId',$student->courseId)
                                    ->first();

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

    
 


    
}
