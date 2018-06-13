<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notice extends Model
{
    protected $fillable = [  'date','centerId', 'title', 'content', 'top',
							 'importance','reviewed', 'reviewedBy',
							 'active','removed', 'updatedBy'
                          ];

    public static function init()
	{
		return [
			'date' =>Carbon::today()->toDateString(),
			'centerId' => 0,
			'title' => '',
			'content' => '',
			'top' => false,
			'active' => true,
			'reviewed'  => false,
            'importance' => 0,
           

		];
	}
	
	public function center() 
	{
		return $this->hasOne('App\Center', 'id' ,'centerId');
    }
	
    
}
