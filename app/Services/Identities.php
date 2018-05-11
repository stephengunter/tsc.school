<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\Course;
use App\Identitiy;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Identities 
{
   
    public function getAll()
    {
        return Identitiy::where('removed',false);
    }
    public function getById($id)
    {   
        return $this->getAll()->where('id',$id)->first();
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id', $ids);
    }

    public function  getByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
    }
        
    public function getIdentitiesOptions()
    {
        $identities=$this->getAll()->get();

        $options = $identities->map(function ($identity) {
            return $identity->toOption();
        })->all();
       
        
        return $options;

        
        
    }
    

   
    
}