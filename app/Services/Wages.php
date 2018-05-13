<?php

namespace App\Services;

use App\Wage;
use DB;

class Wages 
{
    
    public function getAll()
    {
        return Wage::where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }
    public function getByName($name)
    {
        return $this->getAll()->where('name',$name)->first();
    }
    public function getByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
    }

    public function fetchWages()
    {
        
        $wages=$this->getAll()->where('key',$key)->where('active',$active);

        return $wages;
    }
   
    public function options()
    {
        $wages = $this->getAll()->get();
        return $wages->map(function ($wage) {
            return $wage->toOption();
        })->all();
    }
   
    
    
}