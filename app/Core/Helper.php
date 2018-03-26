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
  

}