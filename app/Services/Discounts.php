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
   
    public function getAll()
    {
        return Discount::where('removed',false);
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

    public function fetchDiscounts($key,bool $active = true)
    {
        
        $discounts=$this->getAll()->where('key',$key)->where('active',$active);

        return $discounts;
    }

    public function getOrdered($discounts)
    {
        return $discounts;
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
    

    public  function getLotusDiscount(Center $center)
    {
        if($center->isEast()) return $this->getByCode("lotus");
        return $this->getByCode("lotus-west");
    }

    public function findBestDiscount(Center $center, Term $term, array $identityIds, bool $lotus, int $courseCount)
    {
        $validDiscounts = [];
       
        $lotusDiscount = $this->getLotusDiscount($center);
          
		//此中心擁有的折扣
        $discountsInCenter = $center->discounts;
        $discountsInCenter = $discountsInCenter->where('active',true)->where('min','<=', $courseCount);
       
        if (!$lotus)
        {
            $discountsInCenter =$discountsInCenter->where('id','!=',$lotusDiscount->id);
        }
        
        foreach ($discountsInCenter as $discountInCenter)
        {
            //是否需要身分
            $needIdentityIds = $discountInCenter->identities->pluck('id')->toArray();
            if(count($needIdentityIds)){
                //是否符合身分
                $intersect = array_intersect($needIdentityIds , $identityIds );
                if(count($intersect))   array_push($validDiscounts , $discountInCenter); 
            
            }else{
                // 不需要身分
                array_push($validDiscounts , $discountInCenter); 
                    
            }
            
        }	

        

      
      
        $validDiscounts = collect($validDiscounts);

        //是否在早鳥優惠截止前
        $canBird = $term->canBird(Carbon::today());
        
        if ($canBird)
        {
            //按照第一階段折扣排序
            $sorted = $validDiscounts->sortBy('pointOne')->values()->all();
            return $sorted[0];
        }
        else
        {
            $sorted = $validDiscounts->sortBy('pointTwo')->values()->all();
            return $sorted[0];
            
        }
            
        
    }

   
    
}