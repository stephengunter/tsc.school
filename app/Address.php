<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	protected $primaryKey = 'contactInfoId';

  	protected $fillable = [ 'districtId', 'street', 'updatedBy','ube' ];
	
	public static function init()
	{
		return [
			'cityId' => 0,
			'districtId' => 0,
			'street' => '',
			'zipCode' => '',
			'ube' => ''
		];
	}
    
    public function contactInfo()
    {
		return $this->belongsTo('App\ContactInfo','contactInfoId');
    }

    public function district()
    {
		return $this->belongsTo('App\District','districtId');
	}

	public function fullText()
	{
		$text=$this->street;
		if($this->district){
			$text= $this->district->city->name .  $this->district->name .$text;
		} 
		$this->fullText=$text;
		return  $text;
	}
}
