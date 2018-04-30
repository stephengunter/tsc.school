<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\Lesson;
use App\Weekday;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Teachers;
use App\Services\Volunteers;
use App\Services\Courses;
use App\Services\Lessons;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;
use Exception;

class LessonsController extends Controller
{
    
    public function __construct(Terms $terms,Centers $centers,Courses $courses,Lessons $lessons,
    Users $users,Teachers $teachers, Volunteers $volunteers)
    {
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->lessons=$lessons;
        $this->users=$users;
        $this->teachers=$teachers;
        $this->volunteers=$volunteers;
      
    }

    public function test()
    {
        $term=$this->terms->getActiveTerm();
        if(!$term) return;

        $courses=$this->courses->getStartedCourses($term);

        $date=Carbon::today();
        $weekday=Weekday::where('val',$date->dayOfWeek)->first();

        foreach($courses as $course){
            $classTime=$course->classTimes->where('weekdayId',$weekday->id)->first();
            if($classTime){
                $this->lessons->createLessonFromCourse($course,$classTime,$date);
            }
        }
       
        
    }

    public function loadViewModel(Lesson $lesson)
    {
        $teacherIds=$lesson->getTeacherIds();
        $lesson->teachers=$this->users->getByIds($teacherIds)->get();

        $volunteerIds=$lesson->getVolunteerIds();
        $lesson->volunteers=$this->users->getByIds($volunteerIds)->get();

        $studentIds=$lesson->getStudentIds();
        $lesson->students=$this->users->getByIds($studentIds)->get();

        $lesson->loadViewModel();
        

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

        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $beginDate=Carbon::today();
        if($request->beginDate){
            try {
                $beginDate=new Carbon($request->beginDate);
            }catch (Exception $e) {
                $beginDate=Carbon::today();
            }
        }  

        $endDate=null;
        if($request->endDate){
            try {
                $endDate=new Carbon($request->endDate);
            }catch (Exception $e) {
                $endDate=null;
            }
        }  

        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

       
        if($this->isAjaxRequest()){
            return $this->fetchLessons($term, $center, $course);
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

        $lessons = $this->lessons->fetchLessons($selectedTerm, $selectedCenter, $selectedCourse);
        
        $pageList =$this->getPageList($lessons);

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'title' => '課堂紀錄',
            'menus' => $this->adminMenus('CoursesAdmin'),

            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,  
           
            'list' => $pageList
        ];

        return view('lessons.index')->with($model);
           
    }

    function getPageList($lessons)
    {
        $pageList = new PagedList($lessons);
        //dd($pageList);
       
        foreach($pageList->viewList as $lesson){
            $this->loadViewModel($lesson);
        }  

        return $pageList;
    }

    //Ajax
    function fetchLessons(int $term = 0, int $center = 0, int $course = 0)
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

        $lessons = $this->lessons->fetchLessons($selectedTerm, $selectedCenter, $selectedCourse);
        
        $pageList =$this->getPageList($lessons);

        

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'courseOptions' => $courseOptions,
            'model' => $pageList
        ];

        return response() ->json($model);

        
    }

    public function store(Request $request)
    {
        $values=$request->toArray();
       
       
        $on=$values['on'];
        $off=$values['off'];

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        Lesson::create($values);
        
        return response() ->json();
      
    }
    function validateInputs(array $values)
    {
        $errors=[];
        $on=$values['on'];
        $off=$values['off'];
        
        $on=(int)$on;
        if(!$this->courseInfoes->isValidTimeNumber($on)){
            $errors['on'] = ['時間錯誤'];
        }
        $off=(int)$off;
        if(!$this->courseInfoes->isValidTimeNumber($off)){
            $errors['off'] = ['時間錯誤'];
        }

        if($on >= $off)  $errors['off'] = ['時間錯誤'];

        return $errors;
    }

    public function update(Request $request, $id)
    {
        $lesson=Lesson::findOrFail($id);

        $values=$request->toArray();
       
      

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        $lesson->update($values);

        return response() ->json();
    }

    

    public function destroy($id) 
    {
        $lesson=Lesson::findOrFail($id);
        $lesson->delete();
       
       
        return response() ->json();
    }

    
}
