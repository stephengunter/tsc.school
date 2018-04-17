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
        
        return $total;

    }
    
    public function getCenter()
	{
        if(!count($this->details)) return null;

        return $this->details->first()->course->center;
    }
    public function getDate()
	{
        $this->date = $this->created_at->format('Y-m-d');
        return $this->created_at;
    }

    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }
    
    public function bill() 
	{
		return $this->hasOne(Bill::class,'signupId');
    }
    public function quit() 
	{
		return $this->hasOne(Quit::class,'signupId');
    }

    public function hasDiscount()
    {
        $points=(int)$this->points;
        if($points==100) return false;
        if($points==0) return false;

        return true;
    }

   

    public function pointsText()
    {
        if(!$this->hasDiscount()) return '';

        $points=(int)$this->points;
       
        if($points % 10 == 0){
            return $points/10 . '折';
        }

        return $points . '折';


    }

    public function hasCanceled()
    {
        return $this->status < 1;
    }

  

    public function loadViewModel()
    {
        $this->getDate();
        $this->amount=$this->amount();

        $this->pointsText=$this->pointsText();


        foreach($this->details as $signupDetail){
            $signupDetail->course->fullName();
        } 

        if($this->quit) $this->quit->loadViewModel();
    }

    

}
