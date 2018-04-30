<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Course;
use App\ClassTime;
use App\Lesson;
use App\Services\Courses;
use DB;
use Carbon\Carbon;
use Excel;

class Lessons 
{
    public function __construct(Courses $courses)
    {
        $this->courses=$courses;
        $this->with=['members','course'];
    }
    public function getAll()
    {
        return Lesson::with($this->with); 
    }
    public function getById($id)
    {   
        return Lesson::with($this->with)->find($id);
    }

    public function findByTime()
    {
        whereDay('created_at', '=', date('d'));
    }

    public function createLessonFromCourse(Course $course,ClassTime $classTime,Carbon $date)
    {
        $exist=Lesson::where('courseId',$course->id)->whereDay('date',$date->day)
                      ->where('off', '>=' ,$classTime->on)->first();
        if($exist) return;

        Lesson::create([
            'courseId' => $course->id,
            'status' => 0,
            'date' => $date->toDateString(),

            'on' => $classTime->on,
            'off' => $classTime->off,
        ]);

        $students=$course->activeStudent()->get();
        

        // $user= DB::transaction(function() use($values,$profile) {
        //     $lesson=Lesson::create($values);
            
        //     return $user;
		// });
        
    }
    
    
    

   
    
}