<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [  'key', 'title', 'content', 
							 'importance','reviewed', 'reviewedBy',
							 'active','removed', 'updatedBy'
                          ];

    public static function init()
	{
		return [
			'key' => '',
			'title' => '',
			'content' => '',
		
            'importance' => 0,
           

		];
    }
}
