<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Signup;
use App\Bill;
use App\Student;

use App\Services\Signups;
use App\Services\Bills;
use App\Services\Students;
use App\Http\Requests\BillRequest;

use App\Core\PagedList;
use App\Core\Helper;
use DB;

class BillsController extends Controller
{
    public function __construct(Signups $signups, Bills $bills, Students $students)        
    {
        $this->signups=$signups;
        $this->bills=$bills;
     
        $this->students=$students;
    }

    public function seedPays()
    {
        
        if(!$this->currentUserIsDev()) dd('權限不足');

        $ids = Bill::where('payed',false)->pluck('signupId')->toArray();
        $signups=$this->signups->getByIds($ids)->get();
        foreach ($signups as $signup)
        {
            $num = rand(0 ,100);
            if (($num % 2) == 0) continue;
           
            $amount = $signup->bill->amount;

            $num = rand(0 ,100);
            $payways=\App\Payway::whereIn('code',['credit_net','seven'])->get();

            $payway= (($num % 2) == 0) ? $payways[0] : $payways[1];
            
            if($payway->code=='seven'){
                $this->bills->createBillCode($signup);

                $bill=$this->bills->getById($signup->id);
                $code=$bill->code;
                $amount=$bill->amount;
                $this->bills->payBill($payway, $code, $amount);

            }else{
                $bill=$this->bills->getById($signup->id);
                $code=$bill->code;
                $amount=$bill->amount;
                $this->bills->payBillById($signup->id,$payway,$amount);
            } 

           
          
            foreach($signup->details as $detail){
                $this->students->createStudent($detail->courseId, $signup->userId);
          
            }

           
        }
        

        dd('done');
        
    }

    public function print($id)
    {
      
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);
        

        if($signup->bill->payed) about(404);

        $this->bills->createBillCode($signup);

        return response()->json();

    }
    

   

}
