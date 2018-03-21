<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $fillable = [  'year', 'order', 'name',
							 'number','openDate', 'birdDate','closeDate',
							 'active','removed', 'updatedBy'
                          ];
                          
    
		


    public function	  canBird(Date $date)
	{
		return $date <= $this->birdDate;
	}
}
