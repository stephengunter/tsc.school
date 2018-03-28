<?php

namespace App\Services;

use App\Term;

class Terms 
{
    public function getAll()
    {
        return Term::where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }
    
    public function fetchTerms(bool $active=true)
    {
        return $this->getAll()->where('active',$active);
    }

    public function getTermByNumber($number)
    {
        return $this->getAll()->where('number',$number)->first();
         
    }

    public function getOrdered($terms)
    {
        return $terms->orderBy('active','desc')->orderBy('number','desc');
    }

    public function options()
    {
        $terms=$this->getAll();
        $terms=$this->getOrdered($terms)->get();
        $options = $terms->map(function ($item) {
            return $item->toOption();
        })->all();

        return $options;

    }

   
    
    
    
}