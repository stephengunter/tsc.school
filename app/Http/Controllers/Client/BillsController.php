<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\User;
use App\Profile;
use App\Role;
use App\Term;
use App\Center;
use App\Course;
use App\Signup;
use App\SignupDetail;

use App\Services\Signups;
use App\Services\Bills;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;
use App\Http\Requests\SignupRequest;

use App\Core\PagedList;
use App\Core\Helper;
use DB;

class BillsController extends Controller
{
    public function __construct(Signups $signups, Discounts $discounts, Bills $bills,
     Users $users,Terms $terms,Centers $centers,Courses $courses)        
    {
        $this->signups=$signups;
        $this->bills=$bills;
        $this->discounts=$discounts;
        $this->users=$users;
      
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
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

   

}
