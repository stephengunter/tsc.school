<?php

namespace App\Core;

use App\Center;

trait Centers
{
   
    public function inCenter(Center $center)
    {
        $centerIds=$this->centers()->pluck('id')->toArray();
        return in_array( $center->id ,$centerIds);
    }

    public function addToCenter(Center $center)
	{
        if($this->inCenter($center)) return;
		$this->centers()->attach($center->id);
    }
    
    public function removeFromCenter(Center $center)
	{
        if(!$this->inCenter($center)) return;
		$this->centers()->detach($center->id);
    }

    public function centersText()
    {
        if(!$this->centers) return '';
       
        return join(',',$this->centers->pluck('name')->toArray());
    }
    
}