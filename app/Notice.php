<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [  'centerId', 'title', 'content', 'top',
							 'importance','reviewed', 'reviewedBy',
							 'active','removed', 'updatedBy'
                          ];

    public static function init()
	{
		return [
			'centerId' => '',
			'title' => '',
			'content' => '',
			'top' => false,
            'importance' => 0,
           

		];
    }
    
    public function center() 
	{
		return $this->hasOne('App\Center', 'id' ,'centerId');
    }
}
