<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use \App\Payway;

class Quit extends Model
{
	public static $snakeAttributes = false;

    protected $fillable = [
		'signupId',
		'date', 'tuitions', 'fee', 'auto', 'special',
		'paywayId' , 'status',
		'account_bank','account_branch','account_owner',
		'account_number','account_code',
		'tranId',
        'ps', 'updatedBy'
    ];
                          
                         

	public static function init()
	{
		return [
			'signupId' => 0, 
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

			'special' => 0,
			'ps' => '',
			'updatedBy' => '',

		];
	}	
	
	public static function statuses()
	{
		return array(
            ['value'=> -1 , 'text' => '待處理'],
            ['value'=> 0 , 'text' => '審核中'],
            ['value'=> 1 , 'text' => '已審核'],
            ['value'=> 2 , 'text' => '已完成'],
        );
	}

	public static function isValidStatus($val)
	{
		$val=(int)$val;
		if($val < -1 ) return false;
		if($val > 2 )return false;
		return true;

	}
    
    public function signup()
    {
		return $this->hasOne('App\Signup', 'id' ,'signupId');
	}

	public function payway() 
	{
		return $this->hasOne('App\Payway', 'id' ,'paywayId');
	}

	public function details() 
	{
		return $this->hasMany('App\QuitDetail','quitId');
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
		if($this->tranId) return '轉班退差額';
		if($this->auto) return '課程停開';
		if($this->special) return '學員有特殊原因須退出';
		return '';
	}

	public function countFee()
    {
       
        return  Payway::find($this->paywayId)->getFee($this->tuitions);
    }

	public function amount()
	{
        $total= $this->tuitions - $this->fee;
        
        return $total;

	}

	public function canEdit()
	{
		return $this->status < 0;
	}

	public function canAddDetail()
    {
       
       return $this->status < 0;
    }
	
	
	public function hasDone()
	{
		return $this->status ==2;
	}

	public function updateMoney()
    {
        $tuitions = 0;
        foreach($this->details as $quitDetail){
            $tuitions += $quitDetail->tuition;
		} 
		
		$this->tuitions = $tuitions;

		$this->fee=$this->countFee();
       
        $this->save();


	}
	
	
	
	public function loadViewModel()
    {
		$this->signup->user->profile;

		$this->payway;
		
		$this->amount=$this->amount();
		
		$this->accountInfo=$this->getAccountInfo();

		$this->reason=$this->getReasonText();

        foreach($this->details as $detail){
            $detail->loadViewModel();
        } 
    }
}
