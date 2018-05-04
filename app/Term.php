<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Weekday;
use App\Core\Helper;

class Term extends Model
{
    protected $fillable = [  'year', 'order', 'name',
							 'number','openDate', 'birdDate','closeDate',
							 'active','removed', 'updatedBy'
                          ];
                          
    
		
	public static function init($year)
	{
		return [
			'name' => '',
			'year' => $year,
			'order' => 1,
			'number' => '',
			'openDate' => '',
			'closeDate' => '',
			'birdDate' => '',
			'active' => 0

		];
	}	

	public function courses() 
	{
		return $this->hasMany('App\Course','termId');
    }
	
	

    public function	 canBird($date)
	{
		return $date <= $this->birdDate;
	}

	public function toOption()
    {
        return [ 'text' => $this->number . '學期' ,  'value' => $this->id  ];
	}
	
	public function	 birdDateText()
	{
		if(!$this->birdDate) return '';

		$date=new Carbon($this->birdDate);
		$weekdayText=Weekday::getChineseText($date->dayOfWeek);

		$dateString=$date->subYears(1911)->toDateString();

		if(Helper::str_starts_with($dateString,'0')){
			$dateString=substr($dateString, 1);  
		}

		return $dateString . $weekdayText;
	}

}
