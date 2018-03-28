<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'signupId';

    protected $fillable = [
        'code', 'amount', 'deadLine' , 
        'payed' ,'payway','updatedBy'
    ];

    public static function init()
	{
		return [
			'code' => '',
            'amount' => 0,
            'deadLine' => '',
            'payed' => 0,
            'payway' => 0,
        ];
        
    }
    
    public function signup()
    {
		return $this->belongsTo('App\Signup','signupId');
	}
}
