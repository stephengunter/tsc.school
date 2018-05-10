<?php

namespace App\Core;

use App\Center;

trait Centers
{
   
    public function inCenter(Center $center)
    {
        return $this->inCenterById($center->id);
    }
    public function inCenterById($centerId)
    {
        $centerIds=$this->centers()->pluck('id')->toArray();
        return in_array( $centerId ,$centerIds);
    }

    public function addToCenter(Center $center)
	{
        return $this->addToCenterById($center->id);
    }
    public function addToCenterById($centerId)
	{
        if($this->inCenterById($center->id)) return;
		$this->centers()->attach($centerId);
    }
    public function addToCentersByIds(array $centerIds)
	{
        foreach($centerIds as $centerId){
            $this->addToCenterById($centerId);
        }
    }
    
    public function removeFromCenter(Center $center)
	{
        return $this->removeFromCenterById($center->id);
    }
    public function removeFromCenterById($centerId)
	{
        if(!$this->inCenterById($centerId)) return;
		$this->centers()->detach($centerId);
    }

    public function centersText()
    {
        if(!$this->centers) return '';
       
        return join(',',$this->centers->pluck('name')->toArray());
    }
    
}