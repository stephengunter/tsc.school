<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'signupId';

    protected $fillable = [
        'amount',
        'code',  'deadLine' , 'serial', 'sevenCodes',
        'payed' ,'payDate','paywayId',
        'updatedBy'
    ];

    public static function init()
	{
		return [
			
            'amount' => 0,
    
            'payed' => 0,
            'paywayId' => '',
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

    
    
}
