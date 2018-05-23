<?php

namespace App\Core;

use App\Center;
use App\Course;
use Carbon\Carbon;
use App\Core\Helper;

trait CourseQuitPercent
{
    public function initQuitPercent(Course $course,Carbon $date=null)
    {
        if(!$date) $date=Carbon::today();

        $center=$course->center;

        if($center->isEast()){
            if($course->hasStarted()){
                //已經上課時數加總
                $lessonMinutes=$course->getLessonMinutes();
                if(!$lessonMinutes) return 50;

                $totalMinutes=$course->hours * 60;
                $lessonPercents=$lessonMinutes / $totalMinutes ;
                

                if($lessonPercents<= 0.33333) return 50;
                else return 0;
                
            }else{
                //還未開始,9成
                return 90;
            }

        }else{
            $beginDate=new  Carbon($course->beginDate);
            if(Helper::isSameDate($date,$beginDate)){
                //開課當天,7成
                return 70;
            }else if($course->hasStarted()){
                //已經上課時數加總
                $lessonMinutes=$course->getLessonMinutes();
                if(!$lessonMinutes) return 50;

                $totalMinutes=$course->hours * 60;
                $lessonPercents=$lessonMinutes / $totalMinutes ;

                if($lessonPercents<= 0.33333) return 50;
                else return 0;
                
            }else{
                //還未開始,9成
                return 90;
            }
        }

       

    }

   

    
    
}