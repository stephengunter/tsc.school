<?php

namespace App\Services;
use App\Center;
use App\Notice;

class Notices 
{
    public function getAll()
    {
        return Notice::where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }

    public function getByCenter(Center $center)
    {
        return $this->getAll()->where('centerId',$center->id)->first();
    }

    public function getActiveNotice()
    {
        return $this->getAll()->where('active',true)->first();
    }
    
    public function fetchNotices(bool $active=true)
    {
        return $this->getAll()->where('active',$active);
    }

    

    public function getOrdered($notices)
    {
        return $notices->orderBy('top','desc')
                        ->orderBy('importance','desc')
                        ->orderBy('created_at','desc');
    }

    

   
    
    
    
}