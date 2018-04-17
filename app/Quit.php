<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Quit extends Model
{
	public static $snakeAttributes = false;
	
    protected $primaryKey = 'signupId';

    protected $fillable = [
		'date', 'tuitions', 'fee',
		'paywayId' , 'account' , 'status',
		'reviewed' , 'reviewedBy' ,
        'ps', 'updatedBy'
    ];
                          
                         

	public static function init()
	{
		return [
			
			'date' =>Carbon::today()->toDateString(),
			'fee' => 0,
			'tuitions' => 0,

			'paywayId' => 0,
			'status' => 0,
			'account' => '',
			'ps' => '',
			'updatedBy' => '',

		];
    }	
    
    public function signup()
    {
		return $this->belongsTo('App\Signup','signupId');
	}

	public function payway() 
	{
		return $this->hasOne('App\Payway', 'id' ,'paywayId');
    }

	public function details() 
	{
		return $this->hasMany('App\QuitDetail','signupId');
    }

	public function getCenter()
	{
        return $this->signup->getCenter();
	}

	public function amount()
	{
        $total= $this->tuitions - $this->fee;
        
        return $total;

	}
	
	public function hasDone()
	{
		return $this->status > 0;
	}
	
	public function loadViewModel()
    {
		$this->payway;
		
        $this->amount=$this->amount();

        foreach($this->details as $detail){
            $detail->loadViewModel();
        } 
    }
}
