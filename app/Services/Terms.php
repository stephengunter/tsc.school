<?php

namespace App\Services;

use App\Term;

class Terms 
{
    public function getAll()
    {
        return Term::where('removed',false); 
    }
    
    public function fetchTerms(bool $active=true)
    {
        return $this->getAll()->where('active',$active);
    }

    public function getTermByNumber(int $number)
    {
        return $this->getAll()->where('number',$number)->first();
         
    }

    public function getOrdered($terms)
    {
        return $terms->orderBy('active','desc')->orderBy('number','desc') ->get();
    }

   
    
    
    
}