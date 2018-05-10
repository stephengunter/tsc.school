<?php

namespace App\Core;

use App\City;
use App\Area;

trait Addresses
{
    public function areaOptions()
    {
        $areas=Area::all();

        $options = $areas->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        array_unshift($options, ['text' => '----------' , 'value' =>'']);
       

        return $options;
    }

    public function cityOptions()
    {
        $cities=City::with(['districts'])->get(); 
        return $cities;
    }

    
    
}