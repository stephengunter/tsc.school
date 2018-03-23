<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
	protected $fillable = [  'head', 'oversea', 'areaId','importance',
							 'name','code', 'courseTel','rule',
							 'active','removed', 'updatedBy'
						  ];
						  
    public function contactInfoes() 
	{
		return $this->hasMany('App\ContactInfo','centerId');
	}
	public function area() 
	{
		return $this->hasOne('App\Area','areaId');
    }
}
