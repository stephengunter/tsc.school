<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StudentRequest;
use App\Http\Requests\StudentInfoRequest;

use App\User;
use App\Profile;
use App\Role;
use App\Term;
use App\Center;
use App\Course;
use App\Student;
use App\StudentDetail;

use App\Services\Students;
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



class StudentsController extends Controller
{
    
    public function __construct(Students $students, Discounts $discounts, Bills $bills,
     Users $users,Terms $terms,Centers $centers,Courses $courses)        
    {
        $this->students=$students;
        $this->bills=$bills;
        $this->discounts=$discounts;
        $this->users=$users;
      
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
    }

    function canEdit($student)
    {
        if($this->currentUserIsDev()) return true;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$student->getCenter()]);

        if(count($intersect)) return true;
        return false;

    }
   
    function canEditCourse(Course $course)
    {
        $user=$this->currentUser();
        if(!$user->isTeacher()) return false;
       
        $intersect = $user->teacher->centers->intersect([$course->center]);

        
        if(count($intersect)) return true;
        return false;
    }
   
    public function index()
    {
        $request=request();
        $course=0;
        if($request->course)  $course=(int)$request->course;
        

        $selectedCourse =  $this->courses->getById($course);
        if (!$selectedCourse)  abort(404);

        $canEdit=$this->canEditCourse($selectedCourse);
        if(!$canEdit)  return $this->unauthorized();

        $students = $this->students->getStudentsByCourse($selectedCourse); 

       
       
        $students = $students->orderBy('status','desc')->get();

        return response() ->json($students);

       
        
    }
    

    public function updateScores(Request $form)
    {
       
        $selectedCourse=null;
        $canEditScores=false;

        $current_user=$this->currentUser();

        $students=$form['students'];
        
        foreach($students as $studentScore){
            $student=Student::findOrFail($studentScore['id']);
            if(!$selectedCourse){
                $selectedCourse=Course::findOrFail($student->courseId);
                $canEditScores=$this->canEditCourse($selectedCourse);
                if(!$canEditScores)  return $this->unauthorized();
            } 

            $student->update([
                'score' => $studentScore['score'],
                'updatedBy' => $current_user->id
            ]);
           
        }
       

        return response()->json();
    }

   
}
