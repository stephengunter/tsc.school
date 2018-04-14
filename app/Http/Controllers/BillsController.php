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

        $bills = Bill::all();
        foreach ($bills as $bill)
        {
            $num = rand(0 ,100);
            if (($num % 2) == 0) continue;
            $code = $bill->code;
            $amount = $bill->amount;

            $num = rand(0 ,100);
            $payway = ($num % 2) == 0 ? 1 : 2;

          

            $bill=$this->bills->payBill($code, $amount, $payway);
            $signup=$bill->signup;
          
            foreach($signup->details as $detail){
                $this->students->createStudent($detail->courseId, $signup->userId);
          
            }

           
        }
        

        dd('done');
        
    }


    

   

}
