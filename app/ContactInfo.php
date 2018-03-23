<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $table = 'contactInfoes';

    protected $fillable = [ 'tel', 'fax', 'importance','updatedBy' ];
						 
	

    public function address() 
	{
		return $this->hasOne('App\Address','contactInfoId');
    }

    public function center() 
	{
		return $this->hasOne('App\Center','centerId');
    }

    public function user() 
	{
		return $this->hasOne('App\User','userId');
    }
}
