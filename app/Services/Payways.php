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

    public function initQuitPaywayBySignup($signup)
    {
        $sourcePayway = $signup->bill->payway;
        $code='account';

        

        switch ($sourcePayway->code)
        {
            case 'cash':
                $code= 'account';
                break;
            case 'seven':
                $code = 'account';
                break;
            case 'account':
                $code = 'account';
                break;
            case 'credit_net':
            $code = 'credit_net';
                break;
            case 'credit':
                $code = 'credit';
                break;
        }

        return $this->getByCode($code);

    }

    function getByCode($code)
    {
        return Payway::where('code',$code)->first();
    }

    public function getByText($text)
    {
        $text=strtolower(preg_replace('/\s+/', '', $text));
        $code = '';

        switch ($text)
        {
            case '銀行':
                $code= 'account';
                break;
            case '統一超商':
                $code = 'seven';
                break;
            case 'ok便利商店':
                $code = 'seven';
                break;
            case '萊爾富便利商店':
            $code = 'seven';
                break;
            case '全家便利商店':
                $code = 'seven';
                break;
            case '郵局':
                $code= 'account';
                break;
            
        }

        return $this->getByCode($code);

        
    }
        
    
    
    
}