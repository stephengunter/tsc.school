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
use App\SignupDetail;
use App\Services\Users;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Bills 
{
    public function __construct()
    {
        $this->statuses=array(
            ['value'=> 0 , 'text' => '待繳費'],
            ['value'=> 1 , 'text' => '已繳費'],
            ['value'=> -1 , 'text' => '已取消']
        );
        $this->shopId=config('app.bill.shopId');
        $this->with=['signup'];

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
        
    public function payBill($code, $amount, $payway)
    {
        $bill = $this->getBillByCode($code);
        if ($bill->amount != $amount) abort(500);

        $bill->payed=true;
        $bill->payway=$payway;
       

        DB::transaction(function() use($bill) {
            $bill->save();

            $signup=$bill->signup;
            $signup->status = 1;
           
            $signup->save();
          
        });
        
        return $bill;
    }

    function initBill(Signup $signup)
    {
        $date = Carbon::today();
        $amount=$signup->amount();
        $code=$this->initBillCode($date, $amount);

        return new Bill([
            'code' => $code,
            'amount' => $amount,
            'payed' => false,
            'payway' => 0,
            'deadLine' => $date->addDays(10)
        ]);
        
    }

    function initBillCode($date, $amount)
    {
        $value = rand(10,100*100*100*100);
        return $this->shopId . $value;
    }

    
    
    
}