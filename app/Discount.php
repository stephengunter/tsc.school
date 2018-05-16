<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [  'key','name', 'code', 'min','age',
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

    public function  getPointsText($points)
    {
        $points=(int)$points;

        if(!$points) return '';
        if($points==100) return '';
       
        if($points % 10 == 0){
            return $points/10 . '折';
        }

        return $points . '折';
    }

    public function  loadViewModel()
    {
        $this->pointOneText=$this->getPointsText($this->pointOne);
        $this->pointTwoText=$this->getPointsText($this->pointTwo);
       
    }

	
}
