<?php

namespace App\Http\Controllers;

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
use App\Tran;

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

    function canTrans($student)
    {
        if($student->hasQuit()) return false;
        if($student->tran_id_from) return false;
        return $this->canEdit($student);
        

    }
   
    function canEditCourse(Course $course)
    {
        if($this->currentUserIsDev()) return true;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$course->center]);

        
        if(count($intersect)) return true;
        return false;
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

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $selectedCenter = null;
        $selectedTerm = null;
        $selectedCourse = null;

        $model = [];

        if($this->isAjaxRequest()){
            if ($course)
            {
                $selectedCourse =  $this->courses->getById($course);
                if (!$selectedCourse)  abort(404);

                $selectedTerm = $selectedCourse->term;
                $selectedCenter = $selectedCourse->center;
            }
            else
            {
                $selectedCenter = $this->centers->getById($center);
                $selectedTerm = $this->terms->getById($term);
            }
              
        }else {
       
            $termOptions = $this->terms->options();
            $centerOptions = $this->centers->options();

            if ($center) $selectedCenter = $this->centers->getById($center);
            else $selectedCenter = $this->centers->getById($centerOptions[0]['value']); 

            if ($term) $selectedTerm = $this->terms->getById($term);
            else $selectedTerm =  $this->terms->getById($termOptions[0]['value']); 

            

            if ($course)
            {
                $selectedCourse =  $this->courses->getById($course);
                if ($selectedCourse == null)   abort(404);
            } 

            $model['terms'] = $termOptions;
            $model['centers'] = $centerOptions;
        }

        if (!$selectedTerm) abort(404);
        if (!$selectedCenter) abort(404);

        $courseOptions = $this->courses->options($selectedTerm,$selectedCenter);
       

        if (!$selectedCourse  && count($courseOptions))
        {
            $selectedCourse = $this->courses->getById($courseOptions[0]['value']);
        }

        if($selectedCourse){
            $students = $this->students->getStudentsByCourse($selectedCourse); 
       
            $students = $students->orderBy('status','desc');

            $canEditScores=$this->canEditCourse($selectedCourse);
            
        }else{
            $coursesIds=$this->courses->fetchCourses($selectedTerm->id, $selectedCenter)
                                      ->pluck('id')->toArray();

            $students = $this->students->getAll()->whereIn('courseId',$coursesIds);

            $canEditScores=false;
                             
        }

        $pageList = new PagedList($students);
        foreach($pageList->viewList as $student){
            $student->absenceCount=$student->absenceRecords()->count();
        }  
       
        

        if($this->isAjaxRequest()){
            $model['model'] = $pageList;
            $model['courseOptions'] = $courseOptions;
            $model['canEditScores'] = $canEditScores;
            return response() ->json($model);
        }

        $model['title'] = '學生管理';
        $model['list'] = $pageList;
        $model['menus'] = $this->adminMenus('StudentsAdmin');
        $model['courses'] = $courseOptions;
        $model['canEditScores'] = $canEditScores;
       
        return view('students.index')->with($model);
    }

    

    public function show($id)
    {
       
        $student = $this->students->getById($id);
        if(!$student) abort(404);

        $student->loadViewModel();

        $student->canEdit = $this->canEdit($student);
        $student->canTrans = $this->canTrans($student);

        if($student->canTrans){
            $student->tran=Tran::init();
        }

        return response()->json($student);
        
    }

    public function edit($id)
    {
        $student = $this->students->getById($id);
        if(!$student) abort(404);

        if(!$this->canEdit($student)) $this->unauthorized();

       
      
        $form=[
            'student' => $student

        ];

        return response()->json($form);
       
        
    }

    public function update(Request $request, $id)
    {
        $student = $this->students->getById($id);
        if(!$student) abort(404);

        if(!$this->canEdit($student)) return $this->unauthorized();
       
        $values=$request->student;

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;

        $student->update($values);

       

        return response() ->json();
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
   
   
    public function destroy($id) 
    {
        $student = Student::findOrFail($id);
        if(!$this->canDelete($student)) $this->unauthorized();

        $this->students->deleteStudent($student, $this->currentUserId());
       
       
        return response() ->json();
    }

 


    
}
