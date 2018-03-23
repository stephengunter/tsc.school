<?php

namespace App\Services;

use App\Center;

class Centers 
{
    public function getAll()
    {
        return Center::with(['area','contactInfoes.address'])->where('removed',false); 
    }
    
    public function fetchCenters(bool $oversea = false, int $areaId = 0,bool $active = true)
    {
        $centers=$this->getAll()->where('oversea',$oversea)->where('active',$active);
        if($areaId) $centers=$centers->where('areaId',$areaId);

        return $centers;
    }
    public function  getLocalCenters(bool $active = true)
    {
        return $this->getAll()->where('oversea',false)->where('active',$active);
    }

    

    public function getCenterByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
         
    }

    public function getOrdered($centers)
    {
        return $centers->orderBy('importance','desc');
    }

   
    
    
    
}