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
use App\Account;
use App\Payway;
use App\Signup;
use App\SignupDetail;

use App\Services\Courses;
use App\Services\Signups;
use App\Services\Centers;
use App\Services\Quits;
use App\Services\Bills;
use App\Services\Payways;

use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class QuitsController extends Controller
{
    
    public function __construct(Centers $centers,Bills $bills,Quits $quits,Payways $payways,
                Courses $courses,Signups $signups)             
    {
        $this->centers=$centers;
        $this->quits=$quits;
        $this->bills=$bills;
        $this->courses=$courses;
        $this->signups=$signups;
        $this->payways=$payways;
    }

    function canEdit(Quit $quit)
    {
        if($quit->reviewed) return false;
        if($quit->hasDone()) return false;

        return $this->canAdminCenter($quit->getCenter());
      
    }

    function canQuits(Signup $signup)
    {
        if($signup->status < 1) return false;
        if($signup->quit) return false;

        return $this->canAdminCenter($signup->getCenter());

    }

    function canReview($quit=null)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;


        return $this->currentUser()->isHeadCenterAdmin();

    }

    function canFinish()
    {
        return $this->canReview();
    }
   
    function canDelete(Quit $quit)
    {
        if($quit->hasDone()) return false;
        if($quit->reviewed) return false;
        return $this->canEdit($quit);
    }

    function backPaywayOptions()
    {
        $back=true;
        return $this->payways->paywayOptions($back);
    }

    public function seedQuits()
    {
      
        if(!$this->currentUserIsDev()) dd('權限不足');

        $percentsOptions=$this->quits->percentsOptions();
        $paywayOptions=$this->backPaywayOptions();
        

        $ids = Signup::where('status',1)->pluck('id')->toArray();
       
        $keys = (array_rand($ids,(int)ceil(count($ids)/3 )));
      
        foreach ($keys as $key)
        {
            $num = rand(0 ,100);
            $signupId=$ids[$key];

            $signup=$this->signups->getById($signupId);

            $quitDetails=[];
            foreach($signup->details as $signupDetail){
                $percents=(int)$percentsOptions[array_rand($percentsOptions)]['value']; 
                if(!$percents) continue;

                $actualTuition=$signupDetail->actualTuition();
                $tuition=round($actualTuition * $percents /100);
               
                $quitDetail=new QuitDetail([
                    'signupDetailId' => $signupDetail->id,
                    'percents' => $percents,
                    'tuition' => $tuition,
                ]);
               
                array_push($quitDetails,$quitDetail);
            }

            if(!count($quitDetails)) continue;

            $quitValues=Quit::init();

            $paywayId = $paywayOptions[array_rand($paywayOptions)]['value'];
            $payway=Payway::findOrFail($paywayId);
            if($payway->needAccount()){
                $quitValues['account_number'] = '01345679' . rand(100,100000);
                $quitValues['account_bank'] = '第一銀行';
                $quitValues['account_branch'] = '信義分行';
                $quitValues['account_owner'] = $signup->user->profile->fullname;
                $quitValues['account_code'] = '98754' . rand(100,100000);
            }
            $quitValues['paywayId']=$paywayId;

            $date=new Carbon('2018-4-2'); 
            $date= $date->addDays(rand(0 ,45));
            $quitValues['date']=$date;

            $quit=new Quit($quitValues);

            $quit=$this->quits->createQuit($signup, $quit,$quitDetails);
           
        }
        

        dd('done');
        
    }


    public function index()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

      
        $status=-1;  
        if($request->exists('status'))  $status=(int)$request->status;
       

        $payway=0;
        if($request->payway)  $payway=(int)$request->payway;

        
        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

       
        if($this->isAjaxRequest()){
            return $this->fetchQuits($center, $payway ,$status , $page , $pageSize);
        }

        
        $centerOptions = $this->centers->options(true);
        $selectedCenter = null;
        if ($center)
        {
            $selectedCenter = Center::find($center);
            if (!$selectedCenter) abort(404);
        }

        $selectedPayway=null;
        if($payway){
            $selectedPayway = Payway::find($payway);
            if (!$selectedPayway) abort(404);
        }   
        
        $quits = $this->quits->fetchQuits($selectedCenter,$selectedPayway,$status);
        
        $pageList =$this->getPageList($quits,$page,$pageSize);

        $paywayOptions=$this->backPaywayOptions();
        array_unshift($paywayOptions, ['text' => '所有退款方式' , 'value' =>'0']);

        $canReview= $this->canReview();

        $params=[
            'center' => $center,
            'payway' => $payway,
            'status' => $status,
            'keyword' => $keyword,
            'page' => $page,
            'pageSize' => $pageSize
        ];

        $model=[
            'title' => '退費管理',
            'menus' => $this->adminMenus('SignupsAdmin'),

            
            'centers' => $centerOptions,               
            'statuses' => $this->quits->statusOptions(),
            'payways' => $paywayOptions,

            'canReview' => $canReview,
            'list' => $pageList,

            'params' => $params
        ];

        return view('quits.index')->with($model);
           
    }

    function getPageList($quits,$page,$pageSize)
    {
       
        $pageList = new PagedList($quits,$page,$pageSize);
        
        foreach($pageList->viewList as $quit){
            $quit->loadViewModel();
        }  

        return $pageList;
    }

    //Ajax
    function fetchQuits(int $center, int $payway ,int $status , int $page , int $pageSize)
    {
        $selectedCenter = Center::find($center);

        $selectedPayway=null;
        if($payway)   $selectedPayway = Payway::find($payway);
      
        $quits = $this->quits->fetchQuits($selectedCenter,$selectedPayway,$status);
        

        $pageList =$this->getPageList($quits,$page,$pageSize);

        $canReview= $this->canReview();

        $model=[
            'model' => $pageList,
            'canReview' => $canReview
        ];

        return response() ->json($model);

        
    }

    public function create()
    {
        $request=request();

        $signup=0;
        if($request->signup)  $signup=(int)$request->signup;

        $selectedSignup=$this->signups->getById($signup);
        if(!$selectedSignup) abort(404);

        $payway=$this->payways->initQuitPaywayBySignup($selectedSignup);

        $quit=Quit::init();
        $quit['paywayId'] = $payway->id;

        if($payway->needAccount()){
            $userAccount=$selectedSignup->user->getAccount();
            if($userAccount){
                $quit['account_bank'] = $userAccount->bank;
                $quit['account_branch'] = $userAccount->branch;
                $quit['account_owner'] = $userAccount->owner;
                $quit['account_number'] = $userAccount->number;
                $quit['account_code'] = $userAccount->code;
            }

        }
        
        
        $percentsOptions=$this->quits->percentsOptions();
        $form=[
            'quit' => $quit,
            'paywayOptions' => $this->backPaywayOptions(),
            'percentsOptions' => $percentsOptions
        ];

        return response() ->json($form);
      
    }

    

    function validateQuitInputs(array $values,Payway $payway)
    {
        $errors=[];
       
        if($payway->needAccount()){
            if(!$values['account_bank'])  $errors['quit.account_bank'] = ['必須填寫銀行名稱'];
            if(!$values['account_branch'])  $errors['quit.account_branch'] = ['必須填寫分行'];
            if(!$values['account_owner'])  $errors['quit.account_owner'] = ['必須填寫戶名'];
            if(!$values['account_number'])  $errors['quit.account_number'] = ['必須填寫銀行帳號'];
            if(!$values['account_code'])  $errors['quit.account_code'] = ['必須填寫金資代碼'];
        }

        return $errors;
    }

    public function store(QuitRequest $request)
    {
        $updatedBy=$this->currentUserId();
        $quitValues=$request->getQuitValues();
        $detailsValues=$request->getQuitDetailValues();

        $payway=Payway::findOrFail($quitValues['paywayId']);      

        $errors=$this->validateQuitInputs($quitValues,$payway);
       
        if($errors) return $this->requestError($errors);

        $signup=null;
        $quitDetails=[];
        foreach($detailsValues as $detail){
            $percents=(int)$detail['percents'];
            $signupDetail=SignupDetail::findOrFail($detail['signupDetailId']);
            $actualTuition=$signupDetail->actualTuition();

            
            $tuition=round($actualTuition * $percents /100);
           
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
        $quitValues['auto']=false;
        $quitValues['status']=-1;

        if(!$payway->needAccount()){
            $quitValues['account_bank'] = '';
            $quitValues['account_branch'] = '';
            $quitValues['account_owner'] = '';
            $quitValues['account_number'] = '';
            $quitValues['account_code'] = '';
        }
        $quit=new Quit($quitValues);

        $quit=$this->quits->createQuit($signup, $quit,$quitDetails);
        
        return response()->json($quit);
       
    }

    public function show($id)
    {
        $quit = Quit::findOrFail($id);
        $signup=Signup::findOrFail($id);

        $signup->user->profile;
        $signup->quit->loadViewModel();

        $signup->quit->canEdit = $this->canEdit($quit);
        $signup->quit->canDelete = $this->canDelete($quit);

        return response() ->json($signup);
        
    }

    public function edit($id)
    {
        $quit = $this->quits->getById($id);   
        if(!$quit) abort(404);   
        if(!$this->canEdit($quit)) $this->unauthorized();

        foreach($quit->details as $detail) {
            $detail->loadViewModel();
        }

        $percentsOptions=$this->quits->percentsOptions();
      

        $form=[
            'quit' => $quit,
            'paywayOptions' => $this->backPaywayOptions(),
            'percentsOptions' => $percentsOptions
        ];

        return response()->json($form);
       
        
    }

    public function update(QuitRequest $request, $id)
    {
        $quit = $this->quits->getById($id);   
        if(!$quit) abort(404);   
        if(!$this->canEdit($quit)) $this->unauthorized();

       
        $quitValues=$request->getQuitValues();
        $detailsValues=$request->getQuitDetailValues();

        $payway=Payway::findOrFail($quitValues['paywayId']);      

        $errors=$this->validateQuitInputs($quitValues,$payway);
        if($errors) return $this->requestError($errors);

        $updatedBy=$this->currentUserId();
        $quitValues['updatedBy']=$updatedBy;

       
       
        foreach($detailsValues as $detail){
            $percents=(int)$detail['percents'];
            $detail['percents'] = $percents;
            if($percents){
                $signupDetail=SignupDetail::findOrFail($detail['signupDetailId']);
                $actualTuition=$signupDetail->actualTuition();
               
                $tuition=round($actualTuition * $percents /100);

                $detail['tuition'] = $tuition;
                $detail['updatedBy'] = $updatedBy;
            }
           
        }

        
        $this->quits->updateQuit($quit,$quitValues,$detailsValues);

        return response()->json();
    }

    public function updateStatuses(Request $form)
    {
        if(!$this->canReview()) return $this->unauthorized();

        $updatedBy=$this->currentUserId();
        
       
        $quitIds=array_pluck($form['quits'], 'id');

        foreach($form['quits'] as $quitValues){
            $quit=Quit::find($quitValues['id']);
            $quit->status=$quitValues['status'];
            $quit->updatedBy=$updatedBy;
            $quit->save();
        } 

        return response()->json();


    }

    

    public function updatePS(Request $form)
    {
        $id=$form['id'];
        $ps=$form['ps'];

        $quit = $this->quits->getById($id);   
        if(!$quit) abort(404);   

        if(!$this->canAdminCenter($quit->getCenter())) return $this->unauthorized();
        
        
        $quit->update([
             'ps' => $ps,
             'updatedBy' => $this->currentUserId()
        ]);
        

        return response() ->json();


    }

    public function destroy($id) 
    {
        $quit = $this->quits->getById($id);   
        if(!$quit) abort(404);   
        if(!$this->canDelete($quit)) return $this->unauthorized();

        $this->quits->deleteQuit($quit, $this->currentUserId());
       
       
        return response()->json();
    }

    
 


    
}
