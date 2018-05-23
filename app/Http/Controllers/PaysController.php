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
        $selectedBill=null;
        if($signup) $selectedBill=$this->bills->getById($signup);

        if(!$selectedBill) abort(404);
        
        $pay=Pay::init($selectedBill);

        return response() ->json($pay);
      
    }

    //處理現場繳費
    public function store(Request $form)
    {
        $id = (int)$form['signupId'];
       
        $payway=Payway::find($form['paywayId']);

        $amount=$form['amount'];

        $date=$form['date'];

        $this->bills->payBillById($id,$payway,$amount,$date);

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
