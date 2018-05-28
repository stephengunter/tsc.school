<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bill extends Model
{
   
    protected $fillable = [
        'signupId','amount',
        'serial', 'code',  'deadLine' ,  'sevenCodes',
        'payDate', 'payed' , 'paywayId',
        'updatedBy'
    ];

    public static function init()
	{
		return [
    
           
        ];
        
    }

    public function signup() 
	{
		return $this->hasOne('App\Signup', 'id' ,'signupId');
    }
    public function payway() 
	{
		return $this->hasOne('App\Payway', 'id' ,'paywayId');
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function  loadViewModel()
    {
        if($this->payDate){
            $date=new Carbon($this->payDate);
            $this->payDate = $date->toDateString();
        } 
         
    }


   

    
    
}
