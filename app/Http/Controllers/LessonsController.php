<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\Profile;
use App\Lesson;
use App\LessonMember;
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

    function canEdit(Lesson $lesson)
    {
        if($lesson->reviewed) return false;
        if($this->currentUserIsDev()) return true;

        return $this->canAdminCenter($lesson->getCenter());
    }
    function canEditCenter($center)
    {
        if($this->currentUserIsDev()) return true;

        return $this->canAdminCenter($center);
    }

    function canDelete(Lesson $lesson)
    {
        if($lesson->reviewed) return false;
        return $this->canEdit($lesson);

    }
    function canReview(Lesson $lesson)
    {
        return $this->canReviewCenter($lesson->getCenter());
    }
    function canReviewCenter(Center $center=null)
    {
        if(!$center) return false;

        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canAdminCenter($center);
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

    function getTeacherIds(Lesson $lesson)
    {
        $teacherIds= $lesson->getTeacherIds();
       
        $teachers= $this->teachers->getByIds($teacherIds)->get();
        
        $options = $teachers->map(function ($item) {
            return $item->toOption();
        })->all();

        return  $options;
    }  

    function getVolunteerIds(Lesson $lesson)
    {
        $volunteerIds=$lesson->getVolunteerIds();
        $volunteers= $this->volunteers->getByIds($volunteerIds)->get();

        $options = $volunteers->map(function ($item) {
            return $item->toOption();
        })->all();

        return  $options;
    }  

    function teacherOptions(Lesson $lesson)
    {
        $course=$lesson->course;
        $teachers=$course->teachers;
        
        $options=$teachers->map(function ($item) {
            return $item->toOption();
        })->all();

        if($course->teacherGroup){
            $ids=$lesson->course->teachers->pluck('userId')->toArray();
            
            foreach($course->teacherGroup->teachers as $teacher){
                if(!in_array($teacher->userId,$ids)){
                    array_push($options, $teacher->toOption());
                }
            }
        }
        
        return $options;

    }

    function volunteerOptions(Lesson $lesson)
    {
        $course=$lesson->course;
        $volunteers=$course->volunteers;
        
        $options=$volunteers->map(function ($item) {
            return $item->toOption();
        })->all();

       
        return $options;

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

        $reviewed=false;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $beginDate=null;
        if($request->beginDate){
            try {
                $beginDate=new Carbon($request->beginDate);
            }catch (Exception $e) {
                $beginDate=null;
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
       
        if($this->isAjaxRequest()){
            return $this->fetchLessons($term, $center, $course, $beginDate,$endDate, $reviewed);
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

        $lessons = $this->lessons->fetchLessons($selectedTerm, $selectedCenter, $selectedCourse,$beginDate,$endDate);
        
        $lessons = $lessons->where('reviewed',$reviewed);

        $pageList =$this->getPageList($lessons);

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $canReview=$this->canReviewCenter($selectedCenter);

        
        $model=[
            'title' => '課堂紀錄',
            'menus' => $this->adminMenus('CoursesAdmin'),

            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,  

            'canReview' => $canReview,
           
            'list' => $pageList
        ];

        return view('lessons.index')->with($model);
           
    }

    function getPageList($lessons)
    {
        $lessons=$lessons->orderBy('date');
        $pageList = new PagedList($lessons);
       
        foreach($pageList->viewList as $lesson){
            $this->loadViewModel($lesson);
        }  

        return $pageList;
    }

    //Ajax
    function fetchLessons(int $term = 0, int $center = 0, int $course = 0,$beginDate,$endDate, $reviewed)
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

        $lessons = $this->lessons->fetchLessons($selectedTerm, $selectedCenter, $selectedCourse,$beginDate,$endDate);
        
        $lessons = $lessons->where('reviewed',$reviewed);
        
        $pageList =$this->getPageList($lessons);

        

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'courseOptions' => $courseOptions,
            'model' => $pageList
        ];

        return response() ->json($model);

        
    }

    public function show($id)
    {
        $lesson = $this->lessons->getById($id);
        if(!$lesson) abort(404);

        $this->loadViewModel($lesson);

        foreach($lesson->members as $member){
            $member->name=Profile::find($member->userId)->fullname;
        }

        $lesson->canEdit = $this->canEdit($lesson);
        $lesson->canDelete = $this->canDelete($lesson);
        $lesson->canReview = $this->canReview($lesson);

        return response()->json($lesson);
        
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

    public function edit($id)
    {
        $lesson = $this->lessons->getById($id);
        if(!$lesson) abort(404);

        if(!$this->canEdit($lesson))  return $this->unauthorized();

        

        $teacherIds=$this->getTeacherIds($lesson);
       
        $volunteerIds=$this->getVolunteerIds($lesson);
       
        $teacherOptions = $this->teacherOptions($lesson);
        
        $volunteerOptions = $this->volunteerOptions($lesson);

        $lesson->course->fullName();

        $form=[
            'lesson' => $lesson,
            'teacherIds' =>$teacherIds,
            'volunteerIds' =>$volunteerIds,
            'teacherOptions' => $teacherOptions,
            'volunteerOptions' => $volunteerOptions,
        ];

        return response() ->json($form);
        
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

    public function updateMember(Request $request)
    {
        $id=$request['id'];
        $member=LessonMember::findOrFail($id);
        
        $absence=$request['absence'];
        $ps=$request['ps'];

        $member->absence=$absence;
        $member->ps=$ps;
        $member->updatedBy=$this->currentUserId();

        $member->save();
        
        return response() ->json();
    }

    public function init(Request $form)
    {
        $date=  $form['date'];
        $center=  $form['center'];

        $selectedCenter=$this->centers->getById($center);
        if(!$selectedCenter) abort(404);

        if(!$this->canAdminCenter($selectedCenter))  return $this->unauthorized();

       
        $this->lessons->initLessonsByDate(new Carbon($date));

        return response() ->json();


    }

    public function review(Request $form)
    {
        $reviewedBy=$this->currentUserId();
        
        $lessons=  $form['lessons'];

        if(count($lessons) > 1){
            $lessonIds=array_pluck($lessons, 'id');
            $this->lessons->reviewOK($lessonIds, $reviewedBy);
        }else{
            
            $id=$lessons[0]['id'];
         
            $reviewed=Helper::isTrue($lessons[0]['reviewed']);

            $this->lessons->updateReview($id,$reviewed ,$reviewedBy);
        }

        return response() ->json();


    }

    public function destroy($id) 
    {
        $lesson=Lesson::findOrFail($id);
        $lesson->delete();
       
       
        return response() ->json();
    }

    
}
