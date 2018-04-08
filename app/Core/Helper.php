<?php

namespace App\Core;

class Helper
{
    public static function  str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
    public static function str_ends_with($haystack, $needle)
    {
        return strrpos($haystack, $needle) + strlen($needle) ===
            strlen($haystack);
    }

//$start = 'http';
// $end = 'com';
// $str = 'http://google.com';
// str_starts_with($str, $start); // TRUE
// str_ends_with($str, $end); // TRUE

    public static function toTaipeiYear(int $year)
    {
        return $year - 1911;
    }

    public static function isTrue($val)
    {
        if(is_null($val)) return false;
        
        if(is_numeric($val)){
            if($val) return true;
            return false;
        }
        if( is_string($val) ){
            $val = strtolower($val);
            return $val=='true';
        } 

        return (bool)$val;
    }
    public static function array_has_dupes($array) 
    {
        return count($array) !== count(array_unique($array));
    }

    public static function toTimeString($val)
    {
        $val=(string)$val;
        if (strlen($val) == 3)
        {
            return substr($val,0,1) . ':' . substr($val,1,2);
            
        }
        if (strlen($val) == 4)
        {
            return substr($val,0,2) . ':' . substr($val,2,2);
        }

    }

    public static function buildQuery($url ,array $params)
    {
        if(!$params) return $url;
        
        $query='';
        foreach ($params as $key => $value){
            $query .= $key . '=' . $value . '&';
        }

        
      
        $query=rtrim($query,'&');

         return $url . '?' . $query ;
    }
  

}