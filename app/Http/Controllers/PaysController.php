<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Signup;
use App\Bill;
use App\Pay;
use App\Student;
use App\Payway;

use App\Services\Terms;
use App\Services\Signups;
use App\Services\Bills;
use App\Services\ESuns;
use App\Services\Students;
use App\Http\Requests\BillRequest;

use App\Core\PagedList;
use App\Core\Helper;
use Carbon\Carbon;
use DB;

class PaysController extends Controller
{
    public function __construct(Bills $bills)        
    {
        $this->bills=$bills;
    }
    

    function canEdit($pay)
    {
        if(!$pay->canEdit()) return false;

        if($this->currentUserIsDev()) return true;
        return $this->canAdminCenter($pay->getCenter());

    }

    public function create()
    {
        $request=request();

        $signup=0;
        if($request->signup)  $signup=(int)$request->signup;

        $selectedSignup=Signup::findOrFail($signup);

        $unPayedBill=$selectedSignup->unPayedBills()->first();
        if(!$unPayedBill) abort(404);

        $unPayedBill->payDate=Carbon::today()->toDateString();

        return response()->json($unPayedBill);
      
    }

    //處理現場繳費
    public function store(Request $form)
    {
      
        $id = (int)$form['id'];

        $bill=Bill::findOrFail($id);
        $bill->updatedBy=$this->currentUserId();
       
        $payway=Payway::find($form['paywayId']);

        $amount=$form['amount'];

        $date=$form['payDate'];

        $this->bills->payBill($bill,$payway,$date);

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

    
    

   

}
