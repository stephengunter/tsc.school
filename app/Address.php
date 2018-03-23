<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	protected $primaryKey = 'contactInfoId';

  	protected $fillable = [ 'districtId', 'street', 'updatedBy' ];
		
    public function contactInfo()
    {
		return $this->belongsTo('App\ContactInfo','contactInfoId');
    }

    public function district()
    {
		return $this->belongsTo('App\District','districtId');
    }
}
