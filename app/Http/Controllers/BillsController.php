<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Signup;
use App\Bill;
use App\Student;
use App\Payway;

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
            

           
        }
        

        dd('done');
        
    }

    function canEdit($signup)
    {
        if($this->currentUserIsDev()) return true;

        return $this->canAdminCenter($signup->getCenter());


    }

    //處理現場繳費
    public function pay(Request $form,$id)
    {
        $signup = $this->signups->getById($id);
        if(!$this->canEdit($signup)) return $this->unauthorized();

        if($signup->hasPayed()) abort(500);
        
        $paywayId= $form['paywayId'];
        $payway=Payway::findOrFail($paywayId);

        $amount = $signup->bill->amount;
        $this->bills->payBillById($id,$payway,$amount);

        return response()->json();
    }

    //現場繳費->變成沒繳費
    public function unpay($id)
    {
        
        $signup = $this->signups->getById($id);
        if(!$this->canEdit($signup)) return $this->unauthorized();

        if(!$signup->hasPayed()) abort(500);

        $this->bills->unPayBill($id);

        return response()->json();
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
