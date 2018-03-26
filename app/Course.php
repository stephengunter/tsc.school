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
        'number', 'caution', 'limit','min',
        'beginDate' ,  'endDate' , 'weeks', 'hours',
        'description','target',
        'tuition', 'cost' , 'materials','discount',
        'net' , 'openDate' , 'closeDate',
        'reviewed', 'active',       
        'removed' , 'updatedBy'  
                            
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
          
            'centerId' => $center_id,
            'termId' => $term_id,
          
            'weeks' => $weeks,
            'hours' => $hours,
            'beginDate'=> $begin_date,
            'endDate'=> $end_date,
       
           
            'net'=> 1 ,
            'active'=> 1 ,
           
        ];
    }
    public static function initCourseNumber($serial ,Category $category,Center $center ,Term $term )
    {
        // 8A1071-001
        $serial = (int)$serial;
        if (!$serial) return '';

        $numString = '';
        if ($serial < 10) $numString = '00' .(string)$serial;
        else if ($serial < 100) $numString = '0' . (string)$serial;
        else $numString = (string)$serial;

        return $category->code . $center->code . (string)$term->number .'-' . $numString;

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
    
    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_course','course_id','category_id');
    }
    
    public function classTimes() 
	{
		return $this->hasMany('App\ClassTime','courseId');
    }
    
    public function processes() 
	{
		return $this->hasMany('App\Process','courseId');
    }
    
    public function fullName()
    {
        $fullname=$this->name;
        if($this->level) $fullname .= ' - ' . $this->level;
        $this->fullName=$fullname;
        return $fullname;
    }

    
}
