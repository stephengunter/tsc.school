<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [  'name','code','key','parent' ];
    
	public function centers() 
	{
		return $this->hasMany('App\Center','areaId', 'id');
	}	
	
	
						  
}
