<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $primaryKey = 'userId';
    
	protected $fillable = [
	   'fullname', 'sid', 'gender', 
       'dob' ,  'updatedBy'
    ];
    
    public function user()
    {
		return $this->belongsTo('App\User','userId');
    }
    
    public function setsidAttribute($value) {

		$this->attributes['sid'] = strtoupper($value);
	}
}
