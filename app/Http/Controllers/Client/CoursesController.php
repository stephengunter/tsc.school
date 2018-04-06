<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\Category;
use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;
use App\Services\Categories;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CoursesController extends Controller
{
    
    public function __construct(Courses $courses, 
        Terms $terms,Centers $centers,Categories $categories)
    {
        $this->courses=$courses;
     
        $this->terms=$terms;
        $this->centers=$centers;
        $this->categories=$categories;
       
    }

    function getSelectedCenter()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $selectedCenter = null;
        if ($center) $selectedCenter = $this->centers->getById($center);

        return $selectedCenter;
        
    }

    function getSelectedCategory()
    {
        $request=request();

        $category=0;
        if($request->category)  $category=(int)$request->category;

        $selectedCategory = null;
        if ($category) $selectedCategory = $this->categories->getById($category);

        return $selectedCategory;
        
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
    
    public function index()
    {
        
        $request=request();

        $term=$this->terms->getActiveTerm();

        $withEmpty=false;
        $centerOptions=$this->centers->centerOptions($withEmpty);

        $selectedCenter = $this->getSelectedCenter();
        if (!$selectedCenter)  $selectedCenter = Center::find($centerOptions[0]['value']);

     
        $selectedCategory = $this->getSelectedCategory();

        

        $courses = $this->courses->fetchCourses($term->id ,$selectedCenter);

        $categoryOptions = $this->getCategoryOptions($courses);
     
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

        $centerOptions = array_map(function($item)use($selectedCenter){
            return [ 
                'text' => $item['text'] ,  'value' => $item['value'] , 
                'active' => (int)$item['value'] == $selectedCenter->id,
                'url' => sprintf('/courses?center=%s', $item['value'] )
            ];
       
        }, $centerOptions);

        $categoryOptions = array_map(function($item)use($selectedCenter,$selectedCategory){
            return [ 
                'text' => $item['text'] ,  'value' => $item['value'] , 
                'active' => (int)$item['value'] == $selectedCategory->id,
                'url' => sprintf('/courses?center=%d&category=%s', $selectedCenter->id ,$item['value'])
            ];
       
        }, $categoryOptions);
      

       

            
        $model=[
            'title' => '課程總覽',
            'topMenus' => $this->clientMenus(),
            'menus' => $centerOptions,
            'subMenus' => $categoryOptions,
            'list' => $pageList
        ];

        return view('client.courses.index')->with($model);
    }

   
}
