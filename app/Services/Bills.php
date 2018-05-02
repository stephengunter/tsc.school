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
use App\Events\SignupPayed;
use App\Events\SignupUnPayed;

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

    public function unPayBill($id)
    {
        $bill=$this->getById($id);

        $bill->payed=false;
        $bill->payDate=null;
        $bill->paywayId=null;

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 0;
           
            $signup->save();
          
        });

        event(new SignupUnPayed($bill->signup));
    }

   
    public function payBill(Payway $payway, $code, $amount , $date='')
    {
        if(!$date) $date=Carbon::now();

        $bill = $this->getBillByCode($code);
      
        if ($bill->amount != $amount){
            abort(500);
        }

        $bill->payed=true;
        $bill->payDate=$date;
        $bill->paywayId=$payway->id;
       

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 1;
           
            $signup->save();
          
        });
       
        event(new SignupPayed($bill->signup));
        
        return $bill;
    }
    
    public function payBillById(int $id, Payway $payway, $amount, $date='')
    {
       
        $bill = $this->getById($id);
        
        if ($bill->amount != $amount){
            abort(500);
        }

        if(!$date) $date=Carbon::now();

        $bill->payed=true;
        $bill->payDate=$date;
        $bill->paywayId=$payway->id;
       

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 1;
           
            $signup->save();
          
        });

        event(new SignupPayed($bill->signup));
        
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
             
        $ok=false;
        while(!$ok){
            $serial+=1;
           
            if($serial > 9999) abort(500);

            $exist=Bill::whereDate('deadLine', $deadLineDate->toDateString())
                        ->where('serial', $serial)->first();

                         
            if(!$exist){
                $bill->update([
                    'serial' => $serial
                ]);

                $ok=true;
               
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