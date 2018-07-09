<?php

namespace App\Services;
use App\Center;
use App\Notice;

class Notices 
{
    public function getAll()
    {
        return Notice::with('center')->where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }

    public function getByKey($key)
    {
        $key=strtolower($key);
        $centerIds=Center::where('key',$key)->pluck('id')->toArray();
//dd($centerIds);
  //      dd($this->getAll()->whereIn('centerId',$centerIds)->get());
        
        return $this->getAll()->whereIn('centerId',$centerIds);
    }

    public function getActiveNotice()
    {
        return $this->getAll()->where('active',true);
    }
    
    public function fetchNotices($key,bool $active=true,bool $reviewed=true)
    {
       
        return $this->getByKey($key)
                    ->where('active',$active)
                    ->where('reviewed',$reviewed);
    }

    

    public function getOrdered($notices)
    {
        return $notices->orderBy('top','desc')
                        ->orderBy('importance','desc')
                        ->orderBy('created_at','desc');
    }

    

   
    
    
    
}