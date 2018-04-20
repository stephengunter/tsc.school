<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\User;
use App\Role;
use App\Profile;
use App\Course;
use App\Signup;
use App\Bill;
use App\Payway;
use App\SignupDetail;
use App\Services\ESuns;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Bills 
{
    public function __construct(ESuns $ESuns)
    {
        $this->ESuns=$ESuns;
        $this->with=['signup','payway'];

    }
   
    public function getAll()
    {
        return Bill::with($this->with); 
    }
    public function getById($id)
    {   
        return Bill::with($this->with)->find($id);
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id', $ids);
    }

    public function  getBillByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
    }

   
    public function payBill(Payway $payway, $code, $amount)
    {
        $bill = $this->getBillByCode($code);
        if ($bill->amount != $amount) abort(500);

        $bill->payed=true;
        $bill->payDate=Carbon::now();
        $bill->paywayId=$payway->id;
       

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 1;
           
            $signup->save();
          
        });
        
        return $bill;
    }
    
    public function payBillById(int $id, Payway $payway, $amount)
    {
        $bill = $this->getById($id);
        if ($bill->amount != $amount) abort(500);

        $bill->payed=true;
        $bill->payDate=Carbon::now();
        $bill->paywayId=$payway->id;
       

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 1;
           
            $signup->save();
          
        });
        
        return $bill;
    }

    public function initBill(Signup $signup)
    {
        $date = Carbon::today();
        $amount=$signup->amount();
      

        return new Bill([
           
            'amount' => $amount,
            'payed' => false,
            'payway' => 0,
           
        ]);
        
    }

    function getMaxSerial(Carbon $deadlineDate)
    {
        $maxSerial=Bill::whereDate('deadLine', $deadlineDate->toDateString())
                          ->orderBy('serial','desc')->first();

        if($maxSerial && $maxSerial->serial){
            $maxSerial=(int)$maxSerial->serial;
        } 
        else $maxSerial=0;

        return $maxSerial;
    }
    

    public function createBillCode(Signup $signup)
    {
        $bill=$signup->bill;
        $this->setBillCode($bill);
    }

    function setBillCode(Bill $bill)
    {
       
        //產生虛擬帳號所需資料
        $date = Carbon::today();
        $deadLineDate = $date->addDays(10);

        $serial = $this->getMaxSerial($deadLineDate);

        $exist=true;
        while($exist){
            $serial+=1;
         
            if($serial > 9999) abort(500);

            $exist=Bill::whereDate('deadLine', $deadLineDate->toDateString())
                        ->where('serial', $serial)->first();

                        
            if(!$exist){
                $bill->update([
                    'serial' => $serial
                ]);
            }            
        }

        $code = $this->ESuns->initBillCode($deadLineDate, $bill->amount, $serial);
        $sevenCodes=$this->ESuns->initSevenCodes($deadLineDate, $code,$bill->amount);


        $bill->update([
            'code' => $code,
            'deadLine' => $deadLineDate,
            'sevenCodes' => implode(',', $sevenCodes)
        ]);
    }

    
    
    
    
}