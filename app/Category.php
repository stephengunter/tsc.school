<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable =
    [   'name', 'code', 'importance', 'top',
        'parent', 'active','removed', 'updatedBy'
    ];

    public static function init()
	{
		return [
			'name' => '',
			'code' => '',
			'importance' => 0,
			'top' =>  0,
			'importance' => 0,
			'parent' => 0,
			'active' => 1,

		];
	}	


    public function courses()
    {
        return $this->belongsToMany(Course::class,'category_course','category_id','course_id');
	}

	public function  toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id ];
    }

}
