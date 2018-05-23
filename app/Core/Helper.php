<?php

namespace App\Core;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

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

    public static function checkSID($pid)
    {
        $iPidLen = strlen($pid);
        if(!preg_match("/^[A-Za-z][1-2][0-9]{8}$/",$pid) && $iPidLen != 10)
        {
            return FALSE;
        }
        $head = array("A"=>1,"B"=>10,"C"=>19,"D"=>28,"E"=>37,"F"=>46,"G"=>55,"H"=>64,"I"=>39,"J"=>73,"K"=>82,"M"=>11,"N"=>20,"O"=>48,"P"=>29,"Q"=>38,"T"=>65,"U"=>74,"V"=>83,"W"=>21,"X"=>3,"Z"=>30,"L"=>2,"R"=>47,"S"=>56,"Y"=>12);
        $pid  = strtoupper($pid);
        $iSum  = 0;
        for($i=0;$i<$iPidLen;$i++)
        {
            $sIndex = substr($pid,$i,1);
            $iSum   += (empty($i)) ? $head[$sIndex ] : intval($sIndex) * abs( 9 - base_convert($i,10,9) );
        }
        return ( $iSum  % 10 == 0 ) ? TRUE:FALSE;
    }

    public static function getGenderFromSID($sid)
    {
        $sid  = strtoupper($sid);
        
        $second = substr($sid, 1, 1);
        
        if(is_numeric($second)){
            return (int)$second == 1;
        }else{
            if($second=='A' || $second=='C') return true;
            return false;
        }
    }

    public static function isSameDate(Carbon $dateA, Carbon $dateB)
    {
        if($dateA->year != $dateB->year) return false;
        if($dateA->month != $dateB->month) return false;
        if($dateA->day != $dateB->day) return false;
        return true;
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