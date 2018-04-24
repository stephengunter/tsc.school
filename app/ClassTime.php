<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Core\Helper;

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
	
	public function timeString()
	{
		$timeString= Helper::toTimeString($this->on) . '~' . Helper::toTimeString($this->off);
		$this->timeString=$timeString;
		return $timeString;
	}

	public function fullText()
	{
		$this->fullText= $this->weekday->title . ' ' . $this->timeString();
		return $this->fullText;
	}
}
