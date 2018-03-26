<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
	
	

    public function	 canBird(Date $date)
	{
		return $date <= $this->birdDate;
	}
}
