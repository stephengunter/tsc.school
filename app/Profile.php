<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Core\Helper;

class Profile extends Model
{
    protected $primaryKey = 'userId';
    
	protected $fillable = [
	   'fullname', 'sid', 'gender', 
       'dob' ,  'updatedBy'
	];
	
	public static function init()
	{
		return [
			'fullname' => '',
			'sid' => '',
			'gender' => 1,
			'dob' =>'1975-6-30',

		];
	}
    
    public function user()
    {
		return $this->belongsTo('App\User','userId');
	}
	
	public function setsidAttribute($value) 
	{
		$this->attributes['sid'] = strtoupper($value);
	}

	public function setGenderAttribute($value) 
	{
		$sid=$this->attributes['sid'];
		if(Helper::isTaiwanSID($sid)){
			$this->attributes['gender'] = Helper::getGenderFromSID($sid);
		}else  $this->attributes['gender'] = $value;
		
	}

	public function getAge()
	{
		$today=Carbon::today();
		$dob=new Carbon($this->dob);

		return $dob->diffInYears(Carbon::today());
	}
    
	
}
