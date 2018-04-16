<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $primaryKey = 'userId';
    
    protected $fillable = [  'active', 'removed', 
        'joinDate', 'updatedBy'
    ];

    public static function init()
	{
		return [
			'active' => 0,
			'removed' => 0,
			'joinDate' => '',
			'updatedBy' => '',

		];
	}	
	
	public function user()
    {
        return $this->belongsTo('App\User','userId');
	}
	
	public function addRole()
    {
        $this->user->addRole(Role::volunteerRoleName());
    }

    public function removeRole()
    {
        $this->user->removeRole(Role::volunteerRoleName());
	}
	
	public function  toOption()
    {
        return [ 'text' => $this->user->profile->fullname ,  'value' => $this->userId , 'group' => false ];
    }
}
