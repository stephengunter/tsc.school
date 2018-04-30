<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Course;
use App\Term;
use App\Center;
use App\ClassTime;
use App\Lesson;
use App\LessonMember;
use App\Services\Courses;
use DB;
use Carbon\Carbon;
use App\Core\PagedList;
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

    public function findByDate($query,Carbon $date)
    {
        return $query->whereDay('date',$date->day);
    }

   

    public function createLessonFromCourse(Course $course,ClassTime $classTime,Carbon $date)
    {
        $exist=Lesson::where('courseId',$course->id)->whereDay('date',$date->day)
                      ->where('off', '>=' ,$classTime->on)->first();
        if($exist) return;

        $lesson=new Lesson([
            'courseId' => $course->id,
            'status' => 0,
            'date' => $date->toDateString(),

            'on' => $classTime->on,
            'off' => $classTime->off,
        ]);

        $members=[];

        $students=$course->activeStudent()->get();
        
        foreach($students as $student){
            array_push($members,new LessonMember([
                'userId' => $student->userId,
                'role' => Role::studentRoleName(),

            ]));
        }
        foreach($course->teachers as $teacher){
            array_push($members,new LessonMember([
                'userId' => $teacher->userId,
                'role' => Role::teacherRoleName(),

            ]));
        }

        foreach($course->volunteers as $volunteer){
            array_push($members,new LessonMember([
                'userId' => $teacher->userId,
                'role' => Role::volunteerRoleName(),

            ]));
        }

        DB::transaction(function() use($lesson,$members) {
            
            $lesson->save();
            $lesson->members()->saveMany($members);
        });
        
    }
    
    public function fetchLessons(Term $term,Center $center, Course $course = null)
    {
        $lessons=null;
        if($course){
            $lessons=$this->fetchLessonsByCourse($course);
        }else{
           
            $lessons=$this->fetchLessonsByTermCenter($term, $center);
        }
            
        return $lessons;

    }

    public function fetchLessonsByTermCenter(Term $term, Center $center)
    {
        $courseIds=Course::where('removed',false)->where('termId',$term->id)
                                            ->where('centerId',$center->id)
                                            ->pluck('id')->toArray();

        return $this->getAll()->whereIn('courseId',$courseIds);

       
    }

    public function fetchLessonsByCourse(Course $course)
    {
        
        return $this->getAll()->where('courseId',$course->id);

    }
    
    

   
    
}