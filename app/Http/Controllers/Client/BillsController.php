<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Signup;
use App\Bill;
use App\Student;

use App\Services\Signups;
use App\Services\Bills;
use App\Services\Payways;

use App\Services\ESuns;
use App\Services\Students;
use App\Http\Requests\BillRequest;
use App\BankPost;

use App\Core\PagedList;
use App\Core\Helper;
use Carbon\Carbon;
use DB;

class BillsController extends Controller
{
    public function __construct(Signups $signups, ESuns $ESuns,Bills $bills, Students $students, Payways $payways)        
    {
        $this->signups=$signups;
        $this->ESuns=$ESuns;
        $this->bills=$bills;
        $this->payways=$payways;
        $this->students=$students;
    }



    public function show($id)
    {
        
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        if($signup->userId != $this->currentUserId()) abort(404);

        $signup->loadViewModel();
        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();

        }


        $model=[
            'title' => '繳費',
            'topMenus' => $this->clientMenus(),

            'signup' => $signup
           
        ];

        return view('client.bills.show')->with($model);
    }

    public function print($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        if($signup->userId != $this->currentUserId()) abort(404);

        if($signup->bill->payed) abort(404);

        $this->bills->createBillCode($signup);

        $signup = $this->signups->getById($id);
        
        $signup->loadViewModel();
        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();

        }

        $model=[
            'title' => '列印繳費單',
            'topMenus' => $this->clientMenus(),

            'signup' => $signup
           
        ];

        return view('client.bills.print')->with($model);
    }

    //信用卡繳費
    public function credit($id)
    {
        $this->ESuns->credit();
        dd($id);
    }


    //銀行回傳資料用
    public function store(Request $request)
    {
        $data=explode(',', $request['Data']);

        $date=new Carbon($data[0]);  //20151110
        $from=$data[1];   //銀行 統一超商 ... 
        $serial= $data[2];  //序號
        $code= $data[3];  //虛擬帳號
        $amount= $data[4];  //金額
        $payAt=new Carbon($data[5]);  //20151105132510
        $text=$data[6];  

        $payway=$this->payways->getByText($from);

        //$this->bills->payBill($payway, $code, $amount);

        BankPost::create([
            'date' => $date,
            'from' => $from,
            'serial' => $serial,
            'code' => $code,
            'amount' => $amount,
            'payAt' => $payAt,
            'text' => $text,
        ]);

        return response()->json();

       
    }

   

}
