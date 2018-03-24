<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Address;

class ContactInfo extends Model
{
    protected $table = 'contactInfoes';

    protected $fillable = [ 'tel', 'fax', 'userId' , 'centerId', 'importance','updatedBy' ];
						 
	public static function init()
	{
		return [
			'tel' => '',
			'fax' => '',
			'importance' => 0,
		
			'address'=>Address::init()

		];
	}	

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
