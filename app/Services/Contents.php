<?php

namespace App\Services;

use App\Content;

class Contents 
{
    public function getAll()
    {
        return Content::where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }

    public function getByCenter(Center $center)
    {
        return $this->getAll()->where('centerId',$center->id)->first();
    }

    public function getActiveContent()
    {
        return $this->getAll()->where('active',true)->first();
    }
    
    public function fetchContents($key ,bool $reviewed=true)
    {
        return $this->getAll()->where('key',$key)->where('reviewed',$reviewed);
    }

    public function getOrdered($contents)
    {
        return $contents->orderBy('importance','desc')->orderBy('created_at','desc');
                       
                       
    }

    

   
    
    
    
}