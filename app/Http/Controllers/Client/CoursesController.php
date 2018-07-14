<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Area;
use App\Center;
use App\Category;
use App\User;
use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;
use App\Services\Categories;
use App\Services\Signups;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CoursesController extends Controller
{
    
    public function __construct(Courses $courses, Signups $signups,
        Terms $terms,Centers $centers,Categories $categories)
    {
        $this->courses=$courses;
        $this->signups=$signups;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->categories=$categories;
       
    }

    function getCenters()
    {
        $center_key=config('app.center_key');
        return $this->centers->getCentersByKey($center_key)->where('active',true)->get();
    }

    function getCategoryOptions($courses)
    {

        $categoryIds = array_unique($courses->pluck('categoryId')->toArray());
      
        $categories= $this->categories->getByIds($categoryIds)->get();

        $categoryOptions=$categories->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        return $categoryOptions;
        
    }

    function setTeachers($course)
    {
        $teachers=$course->teachers;
        if($course->teacherGroup) $course->teacherNames=$course->teacherGroup->name;
        if(count($teachers)){
            $names=join(',',$teachers->pluck('user.profile.fullname')->toArray());
            if($course->teacherNames) $course->teacherNames .= $names;
            else $course->teacherNames=$names;
        }

    }
    

    function fetchCourses($term ,$center)
    {
        if(!$term) return Course::where('id' ,'<' , 0);
        $courses = $this->courses->fetchCourses($term->id ,$center);
        return $courses->where('active',true);
    }

    function setCenterOptions($centerOptions, $selectedCenter)
    {
        return array_map(function($item)use($selectedCenter){
            return [ 
                'text' => $item['text'] ,  'value' => $item['value'] , 
                'active' => (int)$item['value'] == $selectedCenter->id,
                'url' => sprintf('/courses?center=%s', $item['value'] )
            ];
       
        }, $centerOptions);
    }

    function setCategoryOptions($categoryOptions,$selectedCenter,$selectedCategory)
    {
        return array_map(function($item)use($selectedCenter,$selectedCategory){
            return [ 
                'text' => $item['text'] ,  'value' => $item['value'] , 
                'active' => (int)$item['value'] == $selectedCategory->id,
                'url' => sprintf('/courses?center=%d&category=%s', $selectedCenter->id ,$item['value'])
            ];
       
        }, $categoryOptions);
    }

    
    function canSignup(Course $course,User $user=null)
    {
       
        //User報名過的課程記錄
        if($user){
            $coursesSignupedIds = [];
            $userSignupDetailRecords = $this->signups->getSignupDetailsByUser($user);
            
            $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
            
            if (in_array($course->id, $coursesSignupedIds)){
                return false;
            }

        }
        
        return true;
    }

    function loadAreaCenters($centers,$selectedCenter=null)
    {
       
        if (!$selectedCenter)  $selectedCenter = $centers[0];
        
        $areaIds=array_unique($centers->pluck('areaId')->toArray());
        $areaIds=array_filter($areaIds,function($item)use($selectedCenter){
            return $item!=$selectedCenter->areaId;
        });
       
        $areas=Area::whereIn('id',$areaIds)->get();
      
        foreach($areas as $area){
            $centersInArea=$centers->where('areaId',$area->id);
            foreach($centersInArea as $center){
                $center->url=sprintf('/courses?center=%s', $center->id);
            }
            $area->centers=$centersInArea;
        }

       
        $centerOptions=$centers->where('areaId',$selectedCenter->areaId)->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        
      
        $centerOptions = $this->setCenterOptions($centerOptions, $selectedCenter);

        return [
            'centerOptions'=> $centerOptions,
            'areas'=>$areas
        ];
    }

    function readIndexRequest()
    {
        $centers=$this->getCenters();

        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;
        $selectedCenter = null;
        if ($center) $selectedCenter = $this->centers->getById($center);
        
        if (!$selectedCenter) $selectedCenter = $centers[0];

        $category=0;
        if($request->category)  $category=(int)$request->category;

        $selectedCategory = null;
        if ($category) $selectedCategory = $this->categories->getById($category);
       

        $selectedTerm=$this->terms->getActiveTerm();

        $areaCenters=$this->loadAreaCenters($centers,$selectedCenter);

        $centerOptions=$areaCenters['centerOptions'];
        $areas=$areaCenters['areas'];

        $requestValues=[
            'term' => $selectedTerm,
            'center' => $selectedCenter,
            'category' => $selectedCategory,
            'centers' => $centers,
            'centerOptions' => $centerOptions,
            'areas' =>$areas
        ];

        return $requestValues;
    }

    function readDetailsRequest($id)
    {
        $course = $this->courses->getById($id);
        if(!$course) abort(404);

        $centers=$this->getCenters();

        $areaCenters=$this->loadAreaCenters($centers,$course->center);

        $centerOptions=$areaCenters['centerOptions'];
        $areas=$areaCenters['areas'];

        $requestValues=[
            'course' => $course,
            'centers' => $centers,
            'centerOptions' => $centerOptions,
            'areas' =>$areas
        ];

        return $requestValues;
    }
    
    public function index()
    {
        $requestValues=$this->readIndexRequest();

        $selectedCenter= $requestValues['center'];
        $selectedTerm= $requestValues['term'];

        $courses = $this->fetchCourses($selectedTerm ,$selectedCenter);

        $categoryOptions = $this->getCategoryOptions($courses);

        $selectedCategory = $requestValues['category'];
        if (!$selectedCategory && count($categoryOptions)) {
            $selectedCategory = Category::find($categoryOptions[0]['value']);
        } 

        if($selectedCategory) $courses = $this->courses->fetchByCategory($courses , $selectedCategory->id);

        $pageList = new PagedList($courses);

        foreach($pageList->viewList as $course){
            $course->fullName();
            $course->loadClassTimes();
            $this->setTeachers($course);
           
        } 

        $centerOptions= $requestValues['centerOptions'];
        $areas= $requestValues['areas'];

        $categoryOptions = $this->setCategoryOptions($categoryOptions,$selectedCenter,$selectedCategory);
        
            
        $model=[
            'title' => $selectedCenter->name . '課程總覽',
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany(),
            'areas' => $areas,
            'menus' => $centerOptions,
            'subMenus' => $categoryOptions,
            'list' => $pageList
        ];

        return view('client.courses.index')->with($model);
    }

    public function show($id)
    {
        $requestValues=$this->readDetailsRequest($id);
        $course= $requestValues['course'];

        $course->fullName();
        $course->loadClassTimes();
        $course->center->loadContactInfo();
        $this->setTeachers($course);
        $course->processes;

        $course->canSignup = $this->canSignup($course,$this->currentUser());
        
       
        foreach($course->center->discounts as $discount){
            $discount->loadViewModel();
        }
       
        $course->term->birdDateText=$course->term->birdDateText();



        $courses = $this->fetchCourses($course->term ,$course->center);


        $categoryOptions = $this->getCategoryOptions($courses);


        $centerOptions= $requestValues['centerOptions'];
        $areas= $requestValues['areas'];

        $categoryOptions = $this->setCategoryOptions($categoryOptions,$course->center, $course->defaultCategory());
        

        $model=[
            'title' => $course->center->name . ' - ' . $course->fullName,
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany(),
            'areas' => $areas,
            'menus' => $centerOptions,
            'subMenus' => $categoryOptions,
            'model' => $course
        ];

        return view('client.courses.details')->with($model);
        
    }

   
}
