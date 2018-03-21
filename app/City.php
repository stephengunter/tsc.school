<?php

namespace App;
use App\District;
use Illuminate\Database\Eloquent\Model;

class City extends Model {
	public $timestamps = false;

	public function districts() {
       
		return $this->hasMany(District::class , 'cityId');
	}

}
