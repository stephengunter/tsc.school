<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payway extends Model
{
    protected $fillable = [
      'name'
    ];

    public function bills() 
	{
		return $this->hasMany('App\Bill','paywayId');
    }
    public function quits() 
	{
		return $this->hasMany('App\Quit','paywayId');
    }
    public function  toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id ];
        
    }

    public function  needAccount()
    {
        
        return $this->name=='匯款';
    }

}
