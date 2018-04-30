<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['userId','photoId' ,'bank', 'branch', 'owner' ,
     'number', 'updatedBy' ];
						 
	public static function init()
	{
		return [
			'bank' => '',
			'branch' => '',
            'owner' => '',
            'number' => '',

		];
    }	
    
    public function user() 
	{
		return $this->hasOne('App\User', 'id' , 'userId');
    }
}
