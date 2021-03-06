<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Notices;
use App\Services\Terms;
use App\Services\Courses;
use App\Core\PagedList;
use App\Core\Helper;

class HomeController extends Controller
{
    public function __construct(Notices $notices,Terms $terms, Courses $courses)
    {
        $this->notices=$notices;
        $this->terms=$terms;
        $this->courses=$courses;
    }

  
    public function index()
    {
        $center_key=config('app.center_key');
        $notices=$this->notices->fetchNotices($center_key);
       
        $notices = $this->notices->getOrdered($notices);
                                
        $page=1;
        $pageSize=5;     
        $notices = new PagedList($notices,$page,$pageSize);
       
     
        $latestCourses =[];
        $recommendCourses = [];

        $term = $this->terms->getActiveTerm(); 
        
        $needRecommend=false;
       
		if($term){
            $courses=$this->courses->fetchCourses($term->id);
            $latestCourses= $courses->orderByRaw('RAND()')->take(4)->get();

            foreach($latestCourses as $course){
                $course->fullName();
                $course->loadClassTimes();
               
            } 

            if($needRecommend){
                $excludeIds = $latestCourses->pluck('id')->toArray();
                $recommendCourses = $courses->whereNotIn('id',$excludeIds)->get();

                if(count($recommendCourses) > 4 ){
                    $recommendCourses = $recommendCourses->random(4);
                }

                foreach($recommendCourses as $course){
                    $course->fullName();
                    $course->loadClassTimes();
                
                } 
            }

            
            
           
        }

       

       
       
        $model=[
            'title' => '',
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany(),
            'noticesModel' => $notices,
            'latestCourses' => $latestCourses,
            'recommendCourses' => $recommendCourses
        ];

        return view('client')->with($model);
    }
}
