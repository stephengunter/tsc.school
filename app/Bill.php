<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'signupId';

    protected $fillable = [
        'code', 'amount', 'deadLine' , 
        'payed' ,'payDate','paywayId','updatedBy'
    ];

    public static function init()
	{
		return [
			'code' => '',
            'amount' => 0,
            'deadLine' => '',
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
