<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Quit extends Model
{
	public static $snakeAttributes = false;
	
    protected $primaryKey = 'signupId';

    protected $fillable = [
		'date', 'tuitions', 'fee', 'auto',
		'paywayId' , 'status',
		'account_bank','account_branch','account_owner',
		'account_number','account_code',
		
        'ps', 'updatedBy'
    ];
                          
                         

	public static function init()
	{
		return [
			
			'date' =>Carbon::today()->toDateString(),
			'fee' => 0,
			'tuitions' => 0,

			'account_bank' => '',
			'account_branch' => '',
            'account_owner' => '',
			'account_number' => '',
			'account_code' => '',
			
			'paywayId' => 0,
			'status' => -1,
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

	public function getUser()
	{
		return $this->signup->user;
	}

	public function getStudentName()
	{
		return $this->getUser()->profile->fullname;
	}

	public function getAccountInfo()
	{
		$text='';
		if($this->account_bank) $text.= $this->account_bank;
		if($this->account_branch) $text.= $this->account_branch;
		if($this->account_number) $text.= $this->account_number;

		if($this->account_owner) $text.= '<br>戶名:' . $this->account_owner;
		if($this->account_code) $text.= '<br>金資代碼:' . $this->account_code;

		return $text;
	}

	public function getReasonText()
	{
		if($this->auto) return '課程停開';
		return '個人因素';
	}

	public function amount()
	{
        $total= $this->tuitions - $this->fee;
        
        return $total;

	}
	
	public function hasDone()
	{
		return $this->status ==2;
	}

	
	
	public function loadViewModel()
    {
		
		$this->payway;
		
		$this->amount=$this->amount();
		
		$this->center=$this->getCenter();

		$this->reason=$this->getReasonText();

        foreach($this->details as $detail){
            $detail->loadViewModel();
        } 
    }
}
