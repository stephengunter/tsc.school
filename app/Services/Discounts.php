<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\Course;
use App\Discount;
use App\Identity;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Discounts 
{
    public function __construct()
    {
        

    }
   
    public function getAll()
    {
        return Discount::with($this->with); 
    }
    public function getById($id)
    {   
        return Discount::with($this->with)->find($id);
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id', $ids);
    }
        
    public function getIdentitiesOptions(Center $center)
    {
        $identityIds=[];
        foreach($center->discounts as $discount)
        {
            $identityIds=array_merge($identityIds,$discount->identities->pluck('id')->toArray());
         
        }

        $identities=Identity::whereIn('id',$identityIds)->get();

        $options = $identities->map(function ($identity) {
            return $identity->toOption();
        })->all();
       
        
        return $options;

        
        
    }

    public function findBestDiscount(Center $center, Term $term, array $identityIds, bool $lotus, int $courseCount)
    {
        return Discount::first();
    }

   
    
}