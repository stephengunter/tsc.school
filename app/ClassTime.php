<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassTime extends Model
{
	protected $table = 'classTimes';

    protected $fillable = [  'courseId', 'weekdayId', 
        'on','off', 'location','updatedBy'
    ];

    public static function init($year)
	{
		return [
			'courseId' => 0,
			'weekdayId' => 0,
			'on' => 1400,
			'off' => 1600,
			'location' => '',
			'updatedBy' => '',

		];
    }	
    
    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
	}
	
	public function weekday() 
	{
		return $this->hasOne('App\Weekday', 'id' ,'weekdayId');
    }
}
