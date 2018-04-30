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

    function canReview(Quit $quit)
    {

        return $this->canReviewCenter($quit->getCenter());

    }

    function canFinish(Quit $quit)
    {
        return $this->canAdminCenter($quit->getCenter());

    }

    function canReviewCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canAdminCenter($center);
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
        

        $signups = Signup::where('status',1)->get();
        foreach ($signups as $signup)
        {
            $num = rand(0 ,100);
            if (($num % 3) != 0) continue;
            
            

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

            $quitValues=Quit::init();

            $paywayId = $paywayOptions[array_rand($paywayOptions)]['value'];
            $payway=Payway::findOrFail($paywayId);
            if($payway->needAccount()){
                $quitValues['account'] = '01345679' . rand(100,100000);
            }

            $quitValues['paywayId']=$paywayId;

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

        $status=0;
        if($request->status)  $status=(int)$request->status;

        $payway=0;
        if($request->payway)  $payway=(int)$request->payway;

        $reviewed=false;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);
       

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

       
        if($this->isAjaxRequest()){
            return $this->fetchQuits($center, $payway ,$status , $reviewed , $page , $pageSize);
        }

        
        $centerOptions = $this->centers->options();
        $selectedCenter = null;
        if ($center)
        {
            $selectedCenter = Center::find($center);
            if (!$selectedCenter) abort(404);
        }
        else
        {
            $selectedCenter = Center::find($centerOptions[0]['value']); 
        } 

        $selectedPayway=null;
        if($payway)   $selectedPayway = Payway::find($payway);
        
        
        
        $quits = $this->quits->fetchQuitsByCenter($selectedCenter);
        
        if($selectedPayway) $quits = $quits->where('paywayId' , $payway);

        $quits = $quits->where('status' , $status)->where('reviewed' , $reviewed);

        $pageList =$this->getPageList($quits,$page,$pageSize);

        $paywayOptions=$this->backPaywayOptions();
        array_unshift($paywayOptions, ['text' => '所有退款方式' , 'value' =>'0']);

        $model=[
            'title' => '退費管理',
            'menus' => $this->adminMenus('SignupsAdmin'),

            
            'centers' => $centerOptions,               
            'statuses' => $this->quits->statusOptions(),
            'payways' => $paywayOptions,

            'canReview' => $this->canReviewCenter($selectedCenter),
            'canFinish' => $this->canAdminCenter($selectedCenter),
            'list' => $pageList
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
    function fetchQuits(int $center, int $payway ,int $status ,bool $reviewed, int $page , int $pageSize)
    {
        $selectedCenter = Center::find($center);
        if (!$selectedCenter) abort(404);

        $selectedPayway=null;
        if($payway)   $selectedPayway = Payway::find($payway);
      
      
        $quits = $this->quits->fetchQuitsByCenter($selectedCenter);

        if($selectedPayway) $quits = $quits->where('paywayId' , $payway);

        $quits = $quits->where('status' , $status)->where('reviewed' , $reviewed);

        $pageList =$this->getPageList($quits,$page,$pageSize);

        $model=[
            'model' => $pageList,
            'canReview' => $this->canReviewCenter($selectedCenter)
        ];

        return response() ->json($model);

        
    }

    public function create()
    {
        $quit=Quit::init();
        $percentsOptions=$this->quits->percentsOptions();
        $form=[
            'quit' => $quit,
            'paywayOptions' => $this->backPaywayOptions(),
            'percentsOptions' => $percentsOptions
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
       

        $errors=$this->validateQuitInputs($quitValues);
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

    public function review(Request $form)
    {

        $reviewedBy=$this->currentUserId();
        
        $quits=  $form['quits'];

        if(count($quits) > 1){
            $quitIds=array_pluck($quits, 'id');

            $quit = $this->quits->getById($quitIds[0]); 
            if(!$this->canReview($quit)) return $this->unauthorized();

            $this->quits->reviewOK($quitIds, $reviewedBy);

        }else{
            
            $id=$quits[0]['id'];

            $quit = $this->quits->getById($id); 
            if(!$this->canReview($quit)) return $this->unauthorized();
         
            $reviewed=Helper::isTrue($quits[0]['reviewed']);

            $this->quits->updateReview($id,$reviewed ,$reviewedBy);
        }
        

        return response() ->json();


    }

    public function finish(Request $form)
    {
        $updatedBy=$this->currentUserId();
        
        $quits=  $form['quits'];

        if(count($quits) > 1){
            $quitIds=array_pluck($quits, 'id');

            $quit = $this->quits->getById($quitIds[0]); 
            if(!$this->canFinish($quit)) return $this->unauthorized();
           
            $this->quits->finishOK($quitIds, $updatedBy);

        }else{
            
            $id=$quits[0]['id'];

            $quit = $this->quits->getById($id); 
            if(!$this->canFinish($quit)) return $this->unauthorized();
         
            $finish=Helper::isTrue($quits[0]['finish']);

            $this->quits->updateFinish($id, $finish ,$updatedBy);
        }
        

        return response() ->json();


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
