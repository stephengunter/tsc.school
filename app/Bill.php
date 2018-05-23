<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'signupId';

    protected $fillable = [
        'code',  'deadLine' , 'serial', 'sevenCodes',
        'payed' ,'payDate',
        'updatedBy'
    ];

    public static function init()
	{
		return [
    
            'payed' => 0,
        ];
        
    }
    
    public function signup()
    {
		return $this->belongsTo('App\Signup','signupId');
    }

    public function pays() 
	{
		return $this->hasMany('App\Pay','signupId');
    }

    public function getAmount()
    {
        return $this->signup->amount();
    }

    public function getAmountShorted()
    {
        $amount=$this->getAmount();
        $moneyPayed=$this->getPaysTotalMoney();
        return $amount - $moneyPayed;
    }

    public function getPaysTotalMoney()
    {
        $total=0;
        foreach($this->pays as $pay){
            $total += $pay->amount;
        }

        return $total;
    }
    

    public function updateStatus()
    {
        $amount=$this->getAmount();

        $payTotalMoney=$this->getPaysTotalMoney();
        if($payTotalMoney >= $amount){
            $this->payed=true;
            $this->payDate=$this->pays()->orderBy('date','desc')->first()->date;
        }else{
            $this->payed=false;
            $this->payDate=null;
        }

        $this->save();

    }

    public function loadViewModel()
    {
        $this->amountPayed = $this->getPaysTotalMoney();
        $this->amountShorted = $this->getAmountShorted();
    }

    
    
}
