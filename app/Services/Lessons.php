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
use App\Weekday;
use App\Services\Courses;
use App\Services\Terms;
use DB;
use Carbon\Carbon;
use App\Core\PagedList;
use Excel;

class Lessons 
{
    public function __construct(Terms $terms,Courses $courses)
    {
        $this->terms=$terms;
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

    public function getByCenter(Center $center)
    {
        $courseIdsInCenter=$center->courses()->pluck('id')->toArray();
        return $this->getAll()->whereIn('courseId',$courseIdsInCenter);
    }

    public function findByDate($query,Carbon $date)
    {
        return $query->whereDate('date',$date->toDateString());
    }

    public function initLessonsByDate(Carbon $date)
    {
        $term=$this->terms->getActiveTerm();
        if(!$term) return;

        $courses=$this->courses->getProcessingCourses($term,$date);

        $weekday=Weekday::where('val',$date->dayOfWeek)->first();
       
        foreach($courses as $course){
           
            $classTime=$course->classTimes->where('weekdayId',$weekday->id)->first();
            if($classTime){
               
                $this->createLessonFromCourse($course,$classTime,$date);
            }
        }

    }

    public function findByCourseDateTime($courseId, ClassTime $classTime,Carbon $date)
    {
        return Lesson::where('courseId',$courseId)->whereDate('date',$date->toDateString())
                      ->where('off', '>=' ,$classTime->on)->first();
        
    }

    public function createLessonFromCourse(Course $course,ClassTime $classTime,Carbon $date)
    {
        $exist=$this->findByCourseDateTime($course->id, $classTime, $date);     
            
        if($exist) return;
        $lesson=new Lesson([
            'courseId' => $course->id,
            'status' => 0,
            'date' => $date->toDateString(),

            'on' => $classTime->on,
            'off' => $classTime->off,
        ]);

        $members=[];

       
        $students = $course->students->filter(function ($student) use($date) {
            return $student->mustInLesson($date);
        })->all();
        
        
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

        if($course->teacherGroup){
            $ids=$course->teachers->pluck('userId')->toArray();
            
            foreach($course->teacherGroup->teachers as $teacher){
                if(!in_array($teacher->userId,$ids)){
                    array_push($members,new LessonMember([
                        'userId' => $teacher->userId,
                        'role' => Role::teacherRoleName(),
        
                    ]));
                }
            }
        }
       
        foreach($course->volunteers as $volunteer){
           
            array_push($members,new LessonMember([
                'userId' => $volunteer->userId,
                'role' => Role::volunteerRoleName(),

            ]));
        }

        DB::transaction(function() use($lesson,$members) {
            
            $lesson->save();
            $lesson->members()->saveMany($members);
        });
        
    }

    public function  updateLesson(Lesson $lesson ,Array $teacherIds=[],Array $volunteerIds=[])
    {
        $lesson->deleteMembersByRole(Role::teacherRoleName());
        $lesson->deleteMembersByRole(Role::volunteerRoleName());

        $members=[];
        foreach($teacherIds as $teacherId){
            array_push($members,new LessonMember([
                'userId' => $teacherId,
                'role' => Role::teacherRoleName(),

            ]));
        }
        foreach($volunteerIds as $volunteerId){
            array_push($members,new LessonMember([
                'userId' => $volunteerId,
                'role' => Role::volunteerRoleName(),

            ]));
        }

        DB::transaction(function() use($lesson,$members) {
            
            $lesson->save();
            $lesson->members()->saveMany($members);
        });
        
        
    }
    
    public function fetchLessons(Term $term,Center $center, Course $course = null,Carbon $beginDate=null,Carbon $endDate=null)
    {
        
        $lessons=null;
        if($course){
            $lessons=$this->fetchLessonsByCourse($course);
        }else{
           
            $lessons=$this->fetchLessonsByTermCenter($term, $center);
        }

        if($beginDate){
            if($endDate && $endDate->gt($beginDate)){
                //區間
                $lessons=$lessons->whereDate('date','>=',$beginDate->toDateString())
                                ->whereDate('date','<=',$endDate->toDateString());
            }else{
                //單一日期
               
                $lessons=$lessons->whereDate('date',$beginDate->toDateString());
            }
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
    
    
    public function reviewOK(array $ids, $reviewedBy)
    {
        $lessons=Lesson::whereIn('id',$ids)->get();
        foreach($lessons as $lesson){
            $lesson->reviewed=true;
            $lesson->reviewedBy=$reviewedBy;
            $lesson->updatedBy=$reviewedBy;
            $lesson->save();
        } 
    }

    public function  updateReview($id,bool $reviewed,int $reviewedBy)
    {
        $lesson=Lesson::find($id);
        $lesson->reviewed=$reviewed;
        $lesson->updatedBy=$reviewedBy;

        if($reviewed){
            $lesson->reviewedBy=$reviewedBy;
        }else{
            $lesson->reviewedBy='';
        }
        $lesson->save();
    }
   
    
}