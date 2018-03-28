<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signup extends Model
{
    public static $snakeAttributes = false;
	
    protected $fillable = [  'userId', 'net', 'points', 'discount' ,
                             'tuitions', 'costs', 'status',
                             'updatedBy', 'removed'
                          ];
                          
                         

	public static function init()
	{
		return [
			'userId' => 0,
			'net' => 0,
			'points' => 0,
			'tuitions' => 0,
			'costs' => 0,
			'discount' => '',
			'status' => 0

		];
	}	

    public function details() 
	{
		return $this->hasMany('App\SignupDetail','signupId');
    }
    
    public function amount()
	{
        $total= $this->tuitions + $this->costs;
        $this->amount=$total;
        return $total;

    }
    
    public function getCenter()
	{
        if(!count($this->details)) return null;

        return $this->details->first();
    }

    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }
    
    public function bill() 
	{
		return $this->hasOne(Bill::class,'signupId');
    }

}
