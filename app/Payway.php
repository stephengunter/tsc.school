<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payway extends Model
{
    protected $fillable = [
        'name'
    ];

    public function pay() 
	{
		return $this->hasMany('App\Pay','paywayId');
    }
    public function quits() 
	{
		return $this->hasMany('App\Quit','paywayId');
    }
    public function  toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id , 'need_account' => $this->need_account];
        
    }

    public function  needAccount()
    {
        return $this->need_account;
    }

    public function  getFee($amount)
    {
        
        if($this->fee_percents){
          
            return round($amount * $this->fee /100);
        }else{
            return $this->fee;
        }
    }

}
