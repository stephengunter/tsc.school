<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\Lesson;
use App\Services\Courses;
use App\Services\Lessons;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class LessonsController extends Controller
{
    
    public function __construct(Courses $courses,Lessons $lessons)
    {
        $this->courses=$courses;
        $this->lessons=$lessons;
      
    }

    public function test()
    {
        
        $course=Course::first();
        $classTime=$course->classTimes()->first();
       
        $date=Carbon::today();
       
        $this->lessons->createLessonFromCourse($course,$classTime,$date);
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

        $status=0;
        if($request->status)  $status=(int)$request->status;

        $payway=0;
        if($request->payway)  $payway=(int)$request->payway;

        $selectedPayway=null;
        if($payway)   $selectedPayway = Payway::find($payway);
        
       

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

       
        if($this->isAjaxRequest()){
            return $this->fetchSignups($term, $center, $course, $keyword,  $status , $payway ,$page , $pageSize);
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
        
        if($keyword){
            $signups =$this->filterSignupsByKeyword($signups, $keyword);
        }


        $signups = $signups->where('status' , $status);
        
        if($selectedPayway){
            $signups = $signups->whereHas('bill', function($q) use($selectedPayway){
                $q->where('paywayId', $selectedPayway->id);
            });
        } 

       
       
        $summary=$this->signups->getSignupSummary($selectedTerm, $selectedCenter, $selectedCourse);

        $pageList =$this->getPageList($signups,$page,$pageSize);


        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $paywayOptions=$this->payways->paywayOptions();
        array_unshift($paywayOptions, ['text' => '所有繳費方式' , 'value' =>'0']);

        $counterPayways=$this->payways->counterPayways();
        $counterPaywayOptions=$counterPayways->map(function ($payway) {
            return $payway->toOption();
        })->all();


        $model=[
            'title' => '報名管理',
            'menus' => $this->adminMenus('SignupsAdmin'),

            'terms' => $termOptions,
            'centers' => $centerOptions,
            'courses' => $courseOptions,                
            'statuses' => $this->signups->statusOptions(),
            
            'payways' => $paywayOptions,
            'counterPayways' => $counterPaywayOptions,

            'summary' => $summary,
            'list' => $pageList
        ];

        return view('signups.index')->with($model);
           
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
