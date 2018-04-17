<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuitRequest;

use App\Term;
use App\Center;
use App\Course;
use App\Quit;
use App\QuitDetail;
use App\Payway;
use App\Signup;
use App\SignupDetail;

use App\Services\Courses;
use App\Services\Signups;
use App\Services\Students;
use App\Services\Quits;
use App\Services\Bills;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class QuitsController extends Controller
{
    
    public function __construct(Bills $bills,Quits $quits,Students $students,Courses $courses,Signups $signups)             
    {
        $this->quits=$quits;
        $this->bills=$bills;
        $this->students=$students;
        $this->courses=$courses;
        $this->signups=$signups;
    }

    function canEdit(Quit $quit)
    {
        if($quit->reviewed) return false;
        if($quit->status > 0) return false;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$quit->getCenter()]);

        if(count($intersect)) return true;
        return false;

    }

    function canQuits(Signup $signup)
    {
        if($signup->status < 1) return false;
        if($signup->quit) return false;

        $centersCanAdmin= $this->centersCanAdmin();
       
        $intersect = $centersCanAdmin->intersect([$signup->getCenter()]);
       
        if(count($intersect)) return true;
        return false;

    }
   
   
    
    public function create()
    {
        $quit=Quit::init();
        $paywayOptions=$this->bills->paywayOptions();

        $form=[
            'quit' => $quit,
            'paywayOptions' => $paywayOptions
        ];

        return response() ->json($form);
      
    }

    function validateQuitInputs($values)
    {
        $errors=[];
       
        $payway=Payway::findOrFail($values['paywayId']);
      
       
        if($payway->needAccount()){
            if(!$values['account'])  $errors['quit.account'] = ['必須填寫銀行帳號'];
        }

        return $errors;
    }

    public function store(QuitRequest $request)
    {
        $updatedBy=$this->currentUserId();
        $quitValues=$request->getQuitValues();
        $detailsValues=$request->getQuitDetailValues();

        $errors=$this->validateQuitInputs($quitValues);
       
        if($errors) return $this->requestError($errors);

        $signup=null;
        $quitDetails=[];
        foreach($detailsValues as $detail){
            $percents=(int)$detail['percents'];
            $signupDetail=SignupDetail::findOrFail($detail['signupDetailId']);
            $actualTuition=$signupDetail->actualTuition();
            $tuition= $actualTuition * $percents /100;

            $quitDetail=new QuitDetail([
                'signupDetailId' => $signupDetail->id,
                'percents' => $percents,
                'tuition' => $tuition,
                'updatedBy' => $updatedBy
            ]);
           
            array_push($quitDetails,$quitDetail);

            $signup=$signupDetail->signup;
        }

        if(!$this->canQuits($signup)) return $this->unauthorized();

        $quitValues['updatedBy']=$updatedBy;
        $quit=new Quit($quitValues);

        $quit=$this->quits->createQuit($signup, $quit,$quitDetails);
        
        return response() ->json($quit);
       
    }

    public function edit($id)
    {
        $quit = $this->quits->getById($id);   
        if(!$quit) abort(404);   
        if(!$this->canEdit($quit)) $this->unauthorized();

        $paywayOptions=$this->bills->paywayOptions();

        $form=[
            'quit' => $quit,
            'paywayOptions' => $paywayOptions
        ];

        return response() ->json($form);
       
        
    }

    
 


    
}
