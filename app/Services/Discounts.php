<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\User;
use App\Role;
use App\Profile;
use App\Course;
use App\Discount;
use App\Services\Users;
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
        
    public function getIdentitiesOptions(Center $center, bool $withEmpty=true)
    {
        $identityIds=[];
        foreach($center->discounts as $discount)
        {
            $identityIds=array_merge($identityIds,$discount->identities->pluck('id')->toArray());
         
        }

        $identities=Discount::whereIn('id',$identityIds)->get();

        $options = $identities->map(function ($identity) {
            return $identity->toOption();
        })->all();

        if($withEmpty) array_unshift($options, ['text' => '無' , 'value' =>'0']);
        
        return $options;

        
        
    }
    
    
}