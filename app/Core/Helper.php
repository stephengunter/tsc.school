<?php

namespace App\Core;
use Illuminate\Database\Eloquent\Collection;

class Helper
{
    public static function getMimeTypes($ext)
    {
        $type = '';

        switch ($ext)
        {
            case 'txt':
                $type= 'text/plain';
                break;
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'doc':
                $type = 'application/vnd.ms-word';
                break;
            case 'docx':
                $type = 'application/vnd.ms-word';
                break;
            case 'xls':
                $type = 'application/vnd.ms-excel';
                break;
            case 'xlsx':
                $type = 'application/vnd.openxmlformatsofficedocument.spreadsheetml.sheet';
                break;
            case 'png':
                $type = 'image/png';
                break;
            case 'jpeg':
                $type = 'image/jpeg';
                break;
            case 'jpg':
                $type = 'image/jpeg';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'csv':
                $type = 'text/csv';
                break;

        }

        return $type;


    }

    public static function  str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
    public static function str_ends_with($haystack, $needle)
    {
        return strrpos($haystack, $needle) + strlen($needle) ===
            strlen($haystack);
    }

    public static function get_file_extension($file_name) {
        return substr(strrchr($file_name,'.'),1);
    }

    public static function  removeExtention($filename)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
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

    public static function getMonthDayString(int $month, int $day)
    {
        $monthString= $month < 10 ? '0' . strval($month) : strval($month);
       
        $dayString= $day < 10 ? '0' . strval($day) : strval($day);
      
        return $monthString . $dayString;

    }

    public static function intToStringLength( $val,int $length,bool $zeroAtFront=true)
    {
        $str=strval((int)$val);
        if($zeroAtFront){
            while (strlen($str) < $length) {
                $str='0' . $str;
            }
        }else{
            while (strlen($str) < $length) {
                $str= $str . '0';
            }
        }
       
        return $str;
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

    public static function mergeCollections(Collection $collectionA,Collection $collectionB)
    {
        foreach ($collectionB as $item)
        {
            if ( ! $collectionA->contains($item->getKey()))
            {
                $collectionA->add($item);
            }
        }
        
        return $collectionA;
    }
  

}