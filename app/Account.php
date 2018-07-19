<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['userId','photoId' ,'bank', 'branch', 'owner' ,'code',
     'number','ps', 'updatedBy' ];
						 
	public static function init()
	{
		return [
			'userId' => '',
			'bank' => '',
			'branch' => '',
            'owner' => '',
			'number' => '',
			'code' => '',
			'ps' => ''
		];
    }	
    
    public function user() 
	{
		return $this->hasOne('App\User', 'id' , 'userId');
	}
	
	public function getPhoto()
	{
		if($this->photoId) return Photo::find($this->photoId);
		return null;
	}
}
