<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lesson;
use App\Teacher;

class Wage extends Model
{
    protected $fillable = ['name', 'code','small_day', 'small_night', 'small_holiday' ,
     'big_day', 'big_night','big_holiday','lecture','updatedBy' ];
						 
	public static function init()
	{
		return [

            'name' => '',
            'code' => '',
			'small_day' => '',
            'small_night' => '',
            'small_holiday' => '',

            'big_day' => '',
            'big_night' => '',
            'big_holiday' => '',

            'lecture' => '',

		];
    }	

    public function teachers() 
	{
		return $this->hasMany('App\Teacher','wageId');
    }

    public function isSpecial()
    {
        return $this->code=='special';
    }

    public function toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id  ];
    }

    public function getActualMoney(Lesson $lesson)
    {
        if($this->isSpecial()) return 0;
        
        $isNightLesson=$lesson->isNightLesson();
        $isBigLesson=$lesson->isBigLesson();
        $isHolidayLesson=$lesson->isHolidayLesson();

        if($isBigLesson){
            if($isHolidayLesson) return $this->big_holiday;
            if($isNightLesson)  return $this->big_night;
            return $this->big_day;
        }else{
            if($isHolidayLesson) return $this->small_holiday;
            if($isNightLesson)  return $this->small_night;
            return $this->small_day;
        }

       
    }
    
}
