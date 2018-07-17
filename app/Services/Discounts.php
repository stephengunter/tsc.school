<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\Course;
use App\User;
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

    public function getDiscoutsByCenter(Center $center)
    {
        return $this->getAll()->where('key',$center->key)->where('active',true);
    }

    public function getOrdered($discounts)
    {
        return $discounts;
    }
        
    public function getIdentitiesOptions(Center $center)
    {
        $discounts=$this->getDiscoutsByCenter($center)->get();
        $identityIds=[];
        foreach($discounts as $discount)
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
        if($center->isWest()) return $this->getByCode("lotus-west");
        return null;
    }

    public function findBestDiscount(Center $center, Term $term, User $user,array $identityIds, bool $lotus, int $courseCount,Carbon $date=null)
    {
       
        if(!$date) $date=Carbon::today();

        $validDiscounts = [];
       
        $lotusDiscount = $this->getLotusDiscount($center);
          
		//此中心擁有的折扣
        $discountsInCenter = $this->getDiscoutsByCenter($center);
        
        $discountsInCenter = $discountsInCenter->where('active',true)->where('min','<=', $courseCount);
       
        if (!$lotus)
        {
            if($lotusDiscount) $discountsInCenter =$discountsInCenter->where('id','!=',$lotusDiscount->id);
            
        }

        $age=$user->getAge($date);
        $discountsInCenter =$discountsInCenter->where('age','<=',$age);
        $discountsInCenter =$discountsInCenter->get();
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
        $canBird = $term->canBird($date);
        
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