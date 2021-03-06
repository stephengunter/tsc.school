<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Center;
use App\Term;
use Carbon\Carbon;

class Course extends Model
{
    public static $snakeAttributes = false;
    
    protected $fillable = [ 
        'termId', 'centerId', 'name', 'level', 
        'teacherGroupId' , 'categoryId',
        'number', 'caution', 'limit','min',
        'beginDate' ,  'endDate' , 'weeks', 'hours',
        'description','target',
        'tuition', 'cost' , 'materials','discount',
        'net' , 'openDate' , 'closeDate',
        'reviewed', 'reviewedBy','active',       
        'removed' , 'updatedBy' , 'ps'
                            
    ];

    public static function init($term_id,$center_id)
    {   
        $begin_date=Carbon::now()->toDateString();
        $end_date=Carbon::now()->addMonths(2)->toDateString();
        $weeks=6;
        $hours=12;
       
        
        return [            
            'name' => '',
            'level' => '',
            'number' => '',
            'discount' => 1,

            'categoryId' => 0,
          
            'centerId' => $center_id,
            'termId' => $term_id,
          
            'weeks' => $weeks,
            'hours' => $hours,
            'beginDate'=> $begin_date,
            'endDate'=> $end_date,
            'ps' => '',
           
            'net'=> 1 ,
            'active'=> 1 ,
           
        ];
    }
    public static function initCourseNumber($serial ,Category $category,Center $center ,Term $term )
    {
        // A1074-0301
        $serial = (int)$serial;
        if (!$serial) return '';

        $categoryString = '';
        if ((int)$category->code < 10) $categoryString = '0' . $category->code;
        else   $categoryString = $category->code;

        $numString = '';
        if ($serial < 10) $numString = '0' .(string)$serial;
        else $numString = (string)$serial;

        return $center->code . (string)$term->number .'-' . $categoryString .$numString;

    }
    

    public function term() 
	{
		return $this->hasOne('App\Term', 'id' ,'termId');
    }
    public function center() 
	{
		return $this->hasOne('App\Center', 'id' ,'centerId');
    }
    
    public function teacherGroup() 
	{
		return $this->hasOne('App\TeacherGroup', 'id' ,'teacherGroupId');
	}

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'course_teacher','course_id','teacher_id');
    }

    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer','course_volunteer','course_id','volunteer_id');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_course','course_id','category_id');
    }
    
    public function classTimes() 
	{
		return $this->hasMany('App\ClassTime','courseId');
    }
    public function lessons() 
	{
		return $this->hasMany('App\Lesson','courseId');
    }

    public function students() 
	{
		return $this->hasMany('App\Student','courseId');
    }
    
    public function processes() 
	{
		return $this->hasMany('App\Process','courseId');
    }

    public function defaultCategory()
    {
        return Category::find($this->categoryId);
    }
    
    public function serial()
    {
        //A1072-0108
        $serial='0';
        if($this->number){
            $arr=explode('-', $this->number); 
            if(count($arr)==2 ){
                $serial = substr($arr[1],2,2);
            } 
        }
        $serial=(int)$serial;

        $this->serial=$serial;
        return $serial;
    }

    public function allTeachers()
    {
       
        $allTeachers= [];  
        foreach ($this->teachers as $teacher) {
            array_push($allTeachers, $teacher);
        }

        if($this->teacherGroup)  array_push($allTeachers, $this->teacherGroup);
        return $allTeachers;
    }

    public function fullName()
    {
        $fullname=$this->name;
        if($this->level) $fullname .= ' - ' . $this->level;
        $this->fullName=$fullname;
        return $fullname;
    }

    public function loadClassTimes()
    {
        foreach($this->classTimes as $classTime){
            $classTime->fullText=$classTime->fullText();            
        }
    }

    public function getLessonMinutes()
    {
        $minutes=0;
        foreach($this->lessons as $lesson){
            $minutes+=$lesson->getMinutes();          
        }
        return $minutes;
    }

    public function toOption()
    {
        return [ 'text' => $this->fullName() ,  'value' => $this->id  ];
    }

    public function activeStudent()
    {
        return $this->students()->where('status' , 1);
    }

    public function isValid()
    {
        if(!$this->reviewed) return false;
        if(!$this->active) return false;
        return true;
    }

    //進行中
    public function isProcessing(Carbon $date=null)
    {
        if(!$this->isValid()) return false;
        if(!$this->hasStarted($date)) return false;
        if($this->hasEnded($date)) return false;
        return true;
    }

    public function hasStarted(Carbon $date=null)
    {
        if(!$date) $date=Carbon::today();
        return $date->gte(new Carbon($this->beginDate));
    }

    public function hasEnded(Carbon $date=null)
    {
        if(!$date) $date=Carbon::today();
        return $date->gt(new Carbon($this->endDate));
    }

    public function canSignup($net=true)
    {
        if($this->removed) return false;   
        if(!$this->reviewed) return false;   
        if(!$this->active) return false;    

        if($net){
            if($this->hasStarted()) return false;
            if(count($this->activeStudent()) >= $this->limit ) return false;
            return true;
        }

        if($this->hasEnded()) return false;

        return true;

        
    }



    
}
