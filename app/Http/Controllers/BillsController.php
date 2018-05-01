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
use App\Services\ESuns;
use App\Services\Students;
use App\Http\Requests\BillRequest;

use App\Core\PagedList;
use App\Core\Helper;
use Carbon\Carbon;
use DB;

class BillsController extends Controller
{
    public function __construct(Signups $signups, Bills $bills, ESuns $ESuns, Students $students)        
    {
        $this->signups=$signups;
        $this->bills=$bills;
        $this->ESuns=$ESuns;
        $this->students=$students;
    }
    public function test()
    {
        $signup=Signup::find(4);
        $this->bills->createBillCode($signup);
       

        $deadlineDate=new Carbon('2018/5/10');
        $amount = 3400;
        $serial=1;
        dd($this->ESuns->initBillCode($deadlineDate, $amount,$serial));
       
    }

    public function seedPays()
    {
       
        if(!$this->currentUserIsDev()) dd('權限不足');

        $ids = Bill::where('payed',false)->pluck('signupId')->toArray();
      
        $keys = (array_rand($ids,(int)ceil(count($ids)/3 )));

     
        foreach ($keys as $key)
        {
            $signupId=$ids[$key];
           
            $signup=$this->signups->getById($signupId);
           
            $amount = $signup->bill->amount;

            $payways=\App\Payway::whereIn('code',['credit_net','seven'])->get();
            $num = rand(0 ,100);
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
