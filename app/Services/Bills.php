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
use App\Pay;
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

    

    public function payBill(Bill $bill,Payway $payway, $date='')
    {
        if(!$date) $date=Carbon::now();

        $bill->update([
            'payed' => true,
            'paywayId' => $payway->id,
            'payDate' => $date
        ]);

        

        $signup=Signup::find($bill->signupId);

      
        $signup->updateStatus();
        
        if($signup->status==1)
        {
            event(new SignupPayed($signup));
        }
        
        return $bill;

    }

   
    public function payBillByCode(Payway $payway, $code, $date='')
    {
        $bill = $this->getBillByCode($code);
      
        return $this->payBill($bill,$payway, $date);
    }
    
    public function payBillById(int $id, Payway $payway, $date='')
    {
        $bill = $this->getById($id);
        return $this->payBill($bill,$payway,$date);
        
    }

    public function initBill()
    {
        return new Bill([
           
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
        
        $bill=$signup->unPayedBills()->first();
        $code=$this->setBillCode($bill);
        return $code;
    }

    function setBillCode(Bill $bill)
    {
       
        //產生虛擬帳號所需資料
        $date = Carbon::today();
        $deadLineDate = $date->addDays(10);

        $serial = $this->getMaxSerial($deadLineDate);
       

        for($i=$serial+1; $i<=9999; $i++){
            $exist=Bill::whereDate('deadLine', $deadLineDate->toDateString())
                        ->where('serial', $i)->first();
            if(!$exist){
                $serial =$i;
                break;
            }  
        }

        $amount=$bill->getAmount();
        
        $code = $this->ESuns->initBillCode($deadLineDate, $amount, $serial);
        $sevenCodes=$this->ESuns->initSevenCodes($deadLineDate, $code,$amount);


        $bill->update([
            'serial' => $serial,
            'code' => $code,
            'deadLine' => $deadLineDate,
            'sevenCodes' => implode(',', $sevenCodes)
        ]);

        return $bill->code;
    }

    
    
    
    
}