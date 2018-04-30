<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    protected $fillable = ['name', 'code','small_day', 'small_night', 'small_holiday' ,
     'big_day', 'big_night','big_holiday','lecture','updatedBy' ];
						 
	public static function init()
	{
		return [

            'name' => '',
            'code' => '',
			'small_day' => '',
            'small_night' => '',
            'small_holiday' => '',

            'big_day' => '',
            'big_night' => '',
            'big_holiday' => '',

            'lecture' => '',

		];
    }	

    public function teachers() 
	{
		return $this->hasMany('App\Teacher','wageId');
    }

    public function isSpecial()
    {
        return $this->code=='special';
    }

    public function toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id  ];
    }
    
}
