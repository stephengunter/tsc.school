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
    function canDelete($student)
    {
        if($student->status>0) return false;
        return $this->canEdit($student);

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

        $bills=\App\Bill::where('payed',true)->get();
        foreach($bills as $bill){
            $signup = \App\Signup::find($bill->signupId);
            foreach ($signup->details as $detail)
            {
                $this->students->createStudent($detail->courseId, $signup->userId);
            }
        }

          
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


       
        if($this->isAjaxRequest()){
            return $this->fetchStudents($term, $center, $course,  $status , $page , $pageSize);
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

        $students = $this->students->fetchStudents($selectedTerm, $selectedCenter, $selectedCourse);
        $students = $students->where('status' , $status);
       
        $summary=$this->students->getStudentSummary($selectedTerm, $selectedCenter, $selectedCourse);

        $pageList =$this->getPageList($students,$page,$pageSize);


        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'title' => '報名管理',
            'menus' => $this->adminMenus('StudentsAdmin'),

            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,                
            'statuses' => $this->students->statusOptions(),

            'summary' => $summary,
            'list' => $pageList
        ];

        return view('students.index')->with($model);
           
    }

    
    function getPageList($students,$page,$pageSize)
    {
        $pageList = new PagedList($students,$page,$pageSize);
        foreach($pageList->viewList as $student){
            $student->loadViewModel();
        }  

        return $pageList;
    }

    //Ajax
    function fetchStudents(int $term = 0, int $center = 0, int $course = 0, int $status = 0, int $page=1 ,int $pageSize=999)
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

        $students = $this->students->fetchStudents($selectedTerm, $selectedCenter, $selectedCourse);
        $students = $students->where('status' , $status);

        $summary=$this->students->getStudentSummary($selectedTerm, $selectedCenter, $selectedCourse);
        
        $pageList =$this->getPageList($students,$page,$pageSize);

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

        $student=Student::init();

        $studentDetails=StudentDetail::init($selectedCourse);

        $selectedCourse->fullName();
        $selectedCourse->loadClassTimes();
        $studentDetails['course'] =$selectedCourse;

        $student['details']=[$studentDetails];
        $form=[
            'student' => $student,
            'user' => User::init(),
            'courseOptions' => $this->courses->options($selectedCourse->term,$selectedCourse->center),
            'identityOptions' => $this->discounts->getIdentitiesOptions($selectedCourse->center),
            'courseIds' => [$selectedCourse->id],
            'identityIds' => [],
            'lotus' => false
        ];

        return response() ->json($form);
      
    }

    public function store(StudentRequest $request)
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

        $studentDetails=[];

        $selectedCourses=$this->courses->getByIds($courseIds)->get();

          //User報名過的課程記錄
        $coursesStudentedIds = [];
        $userStudentDetailRecords = $this->students->getStudentDetailsByUser($user);
        $coursesStudentedIds = $userStudentDetailRecords->pluck('courseId')->toArray();
        foreach ($selectedCourses as $selectedCourse)
        {

            if (in_array($selectedCourse->id, $coursesStudentedIds)){
                $errors['courseIds'] = ['此學員已經報名過課程' . $selectedCourse->fullName() ];
                return $this->requestError($errors);
            }

            $detail = new StudentDetail([
                'courseId' => $selectedCourse->id,
                'tuition' => $selectedCourse->tuition,
                'cost' => $selectedCourse->cost,
                'updatedBy' => $updatedBy

            ]);
            array_push($studentDetails,$detail);
        }

        $student=new Student(['userId' => $userId , 'net'=> false , 'updatedBy' => $updatedBy ]);

        $this->setStudentMoney($student, $studentDetails ,$selectedCourses, $identityIds,  $request['lotus']);

       
        $student=$this->students->createStudent($student,$studentDetails);
        
        return response() ->json($student);
       
    }

    function setStudentMoney(Student $student, array $studentDetails ,$courses, $identityIds, $lotus)
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
        
           
            $student->points = $bestDiscount->pointOne;

            if ($student->points < 100)
            {
                $student->discount = $bestDiscount->name;
            }

            if ($bestDiscount->bird())
            {
                $student->discount .= " - 早鳥優惠";
            }

        }else{
        
            if ($student->points < 100)
            {
                $student->discount = $bestDiscount->name;
            }

            $student->points = $bestDiscount->pointTwo;
        }

        $tuitions = 0;
        foreach($studentDetails as $studentDetail) {
            $tuitions += $studentDetail['tuition'];
        }
        
        $student->tuitions = $tuitions * $student->points / 100;
        $student->costs = 0;
        foreach($studentDetails as $studentDetail) {
            if($studentDetail->cost)  $student->costs += $studentDetail->cost;
        }

    }


    public function show($id)
    {
        $student = $this->students->getById($id);
        if(!$student) abort(404);

        $student->loadViewModel();

        $student->canEdit = $this->canEdit($student);
        $student->canDelete = $this->canDelete($student);

        return response() ->json($student);
        
    }
   
    public function report()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->course);

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

        $studentReports=array_map(function($course){
            return [
                'course' => $course,
                'studentCount' => 6
            ];
        }, $pageList->viewList);
        
        $pageList->viewList=$studentReports;

        if($this->isAjaxRequest()){
          
            $model['model'] = $pageList;
            return response() ->json($model);
        }

        $model['title'] = '報名統計';
        $model['menus'] = $this->adminMenus('StudentsAdmin');
        $model['list'] = $pageList;

        return view('students.report')->with($model);

       
    }

    public function destroy($id) 
    {
        $student = Student::findOrFail($id);
        if(!$this->canDelete($student)) $this->unauthorized();

        $this->students->deleteStudent($student, $this->currentUserId());
       
       
        return response() ->json();
    }

 


    
}
