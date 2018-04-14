<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
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
}
