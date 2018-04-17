<?php

namespace App\Services;

use App\Payway;
use Carbon\Carbon;
use App\Core\Helper;

class Payways 
{

    public function paywayOptions($back=false)
    {
        $payways=null;
        if($back) $payways=Payway::where('back',true)->get();
        else $payways=Payway::all();
        
        $options = $payways->map(function ($payway) {
            return $payway->toOption();
        })->all();

       
        return $options;
    }
        
    
    
    
}