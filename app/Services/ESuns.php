<?php

namespace App\Services;

use App\Signup;
use App\Bill;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ESuns 
{
    public function __construct()
    {
        
        $this->shopId=config('app.bill.shopId'); //虛擬帳號商店代碼
        $this->sevenCode=config('app.bill.sevenCode');  //超商代碼

        $this->creditShopId=config('app.credit.shopId'); 
        $this->creditMackey=config('app.credit.mackey');  

    }

    public function credit()
    {
        $url= 'https://acqtest.esunbank.com.tw/ACQTrans/esuncard/txnf014s';
        $mackey='IVIPUZ2FTROXRQUAVGBD9QTT8YKT7LZZ';
        
        $data = [
            'MID' => '8089024730',  //測試用特店代碼
            'TID' => 'EC000001',
            'ONO' => '117', 
            'TA' => 450,
            'U' => 'http://203.64.35.83/bills/credit'            
        ];

        $mac = hash('sha256', json_encode($data) . $mackey );
       

        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'data' => $data,
                'mac' => $mac,
                'ksn' => 1,
            ]
        ]);
       
        $body =  json_decode($response->getBody());
        dd($body);
        return $body;
    }

    function getSevenCode($amount)
    {
        if($amount<=20000) return $this->sevenCode[0];
        return $this->sevenCode[1];
    }
    
    public function initBillCode(Carbon $deadlineDate, $amount,$serial)
    {
       
        $dateString=Helper::getMonthDayString($deadlineDate->month,$deadlineDate->day);

        $serialString=Helper::intToStringLength($serial,4);
        
       
        // 銷帳編號
        $code=$this->shopId . $dateString . $serialString;
        
        //檢查碼
        $checkCode=$this->initCheckCode($code,$amount);
       
        return $code . $checkCode;
    }


    function initCheckCode($code, $amount)
    {
        $codeResult=$this->countResult($code);
        $amountResult=$this->countResult($amount);
        
        $result=$codeResult + $amountResult;

        return substr(strval($result), -1);
        
    }

    function countResult($str)
    {
        $result=0;

        $str=strval($str);
        $arr =str_split(strrev($str));

        $weight=1;
        foreach($arr as $num){
            if($weight>9) $weight=1;

            $result += $weight * (int)$num;

            $weight++;
        }

        return $result;
    }

    function initSevenCodes(Carbon $deadline, $code, $amount)
    {
        
        $year=Helper::toTaipeiYear($deadline->year);
        $year=substr(strval($year), -2); 
        $monthDay=Helper::getMonthDayString($deadline->month, $deadline->day);

      
        $first= $year . $monthDay . $this->getSevenCode($amount);
       
        $zeroAtFront=false;
        $second=Helper::intToStringLength($code, 16,$zeroAtFront);
       
        $third= $monthDay . 'XX' . Helper::intToStringLength($amount, 9);
      
        $checkOne=$this->countSevenFirstCheckCode($first,$second,$third);  //第一位檢查碼
        $checkTwo=$this->countSevenSecondCheckCode($first,$second,$third);  //第二位檢查碼

        
        $third= $monthDay . $checkOne . $checkTwo . Helper::intToStringLength($amount, 9);
       
        return [$first,$second,$third]; 

    }

    function getNumber($val)
    {
        $codes=array(
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'F' => 6,
            'G' => 7,
            'H' => 8,
            'I' => 9,

            'J' => 1,
            'K' => 2,
            'L' => 3,
            'M' => 4,
            'N' => 5,
            'O' => 6,
            'P' => 7,
            'Q' => 8,
            'R' => 9,

            
            'S' => 2,
            'T' => 3,
            'U' => 4,
            'V' => 5,
            'W' => 6,
            'X' => 7,
            'Y' => 8,
            'Z' => 9,
        );
        if(array_key_exists($val,$codes)) return $codes[$val];
        return (int)$val;
    }

    function countSevenFirstCheckCode($first,$second,$third)
    {
        $sum=0;
        $arr=str_split($first);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) != 0){
              
               $sum+=$this->getNumber($arr[$i-1]);    
            }         
        }

        $arr=str_split($second);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) != 0){
               $sum+= (int)$arr[$i-1];    
            }         
        }

        $arr=str_split($third);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) != 0  && $i!=5){
               $sum+= (int)$arr[$i-1];    
            }         
        }

        $remainder=$sum % 11;
        if($remainder==0) return 'A';
        if($remainder==10) return 'B';
        return strval($remainder);

    }

    function countSevenSecondCheckCode($first,$second,$third)
    {
        $sum=0;
        $arr=str_split($first);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) == 0){
                $sum+=$this->getNumber($arr[$i-1]);    
            }         
        }

        $arr=str_split($second);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) == 0){
               $sum+= (int)$arr[$i-1];    
            }         
        }

        $arr=str_split($third);
        for($i = 1; $i <= count($arr); ++$i) {
            if(($i % 2) == 0  && $i!=6){
               $sum+= (int)$arr[$i-1];    
            }         
        }

        $remainder=$sum % 11;
        if($remainder==0) return 'A';
        if($remainder==10) return 'B';
        return strval($remainder);

    }

    
    
    
}