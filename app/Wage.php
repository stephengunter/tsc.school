<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    protected $fillable = ['userId', 'bank', 'branch', 'owner' ,
     'account', 'money','updatedBy' ];
						 
	public static function init()
	{
		return [
			'bank' => '',
			'branch' => '',
            'owner' => '',
            'account' => '',
            'money' => 0,

		];
    }	
    
    public function user() 
	{
		return $this->hasOne('App\User','userId');
    }
}
