<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [  'name', 'code', 'min',
        'pointOne','pointTwo','prove', 'ps',
        'active','removed', 'updatedBy'
    ];
   
    public function identities()    
    {
        return $this->belongsToMany(Identity::class,'discount_identity','discount_id','identity_id');
    }
    
  
    public function centers()
    {
        return $this->belongsToMany(Center::class,'center_discount','discount_id','center_id');
    }

    public function bird()
    {
        return $this->pointTwo > $this->pointOne;
    }

    public function  toOption()
    {
		return [ 'text' => $this->name ,  'value' => $this->id ];
       
    }


	
}
