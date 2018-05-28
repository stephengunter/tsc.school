<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Course;
use App\Quit;

class Tran extends Model
{
	public static $snakeAttributes = false;

    protected $fillable = [
		'date', 'signupDetailId', 'courseId' , 
		'reviewed', 'reviewedBy',
        'ps', 'updatedBy'
    ];

    public static function init()
	{
		return [
			'date' =>Carbon::today()->toDateString(),
			'signupDetailId' => 0,
			'courseId' => 0,
			'ps' => '',
            'isPay' => true,
            'studentId' => 0,
			'updatedBy' => ''

		];
	}

    public function signupDetail() 
	{
		return $this->hasOne('App\SignupDetail', 'id' ,'signupDetailId');
    }

    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
	}

	public function getSignup()
	{
		return $this->signupDetail->signup;
	}

	public function  getUser()
	{
		return $this->signupDetail->signup->user;
	}

	public function getCenter()
	{
		return $this->course->center;
	}
	public function getQuit()
	{
		return Quit::where('tranId',$this->id)->first();
	}

	public function  canDelete()
	{
		return true;
	}

	public function  getMustBackAmount()
	{
		$signup=$this->getSignup();
		$amount=$signup->getAmountShorted();

		if($amount<0) return abs($amount);

		return 0;
	}
	
	public function  loadViewModel()
	{
		$this->course->fullName();

		$this->formCourse=$this->signupDetail->course;
		$this->formCourse->fullName();

		$this->user=$this->getUser();
		$this->user->profile;

		$this->quit=$this->getQuit();
		

		$this->amountMustPay=0;
		$this->amountMustBack=0;
		$signup=$this->getSignup();
		$amount=$signup->getAmountShorted();
		if($amount>0) $this->amountMustPay = $amount;
		if($amount<0) $this->amountMustBack = abs($amount);

	}
	
}
