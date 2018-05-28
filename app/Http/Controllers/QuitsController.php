<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuitRequest;

use App\User;
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
use App\Services\Users;
use App\Services\Quits;
use App\Services\Bills;
use App\Services\Payways;

use App\Core\CourseQuitPercent;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class QuitsController extends Controller
{
    use CourseQuitPercent;

    public function __construct(Centers $centers,Bills $bills,Quits $quits,Payways $payways,
                Courses $courses,Users $users,Signups $signups)             
    {
        $this->centers=$centers;
        $this->quits=$quits;
        $this->bills=$bills;
        $this->users=$users;
        $this->courses=$courses;
        $this->signups=$signups;
        $this->payways=$payways;
    }

    function canEdit(Quit $quit)
    {
        if(!$quit->canEdit()) return false;
        return $this->canAdminCenter($quit->getCenter());
      
    }

    function canQuits(Signup $signup)
    {
        if($signup->status < 1) return false;

        return $this->canAdminCenter($signup->getCenter());

    }

    function canReview()
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
        
        $banks=['第一銀行','國泰世華','中國信託','玉山銀行'];
        $branches=['信義分行','仁愛分行','和平分行','忠孝分行'];
        
        $ids = Signup::where('status',1)->pluck('id')->toArray();
       
        $keys = (array_rand($ids,(int)ceil(count($ids)/3 )));
      
        foreach ($keys as $key)
        {
            $num = rand(0 ,100);
            $signupId=$ids[$key];

            $signup=$this->signups->getById($signupId);
           
            $special= ($num % 2) == 0;

            $quitDetails=[];
            foreach($signup->details as $signupDetail){
                if($signupDetail->canQuit()){
                    
                    $percents=0;
                    if($special)   $percents=(int)$percentsOptions[array_rand($percentsOptions)]['value']; 
                    else $percents= $this->initQuitPercent($signupDetail->course);

                    $actualTuition=$signupDetail->actualTuition();
                    $tuition=round($actualTuition * $percents /100);

                    $quitDetail=new QuitDetail([
                        'signupDetailId' => $signupDetail->id,
                        'percents' => $percents,
                        'tuition' => $tuition,
                    ]);
                

                }
               
                array_push($quitDetails,$quitDetail);
            }

            if(!count($quitDetails)) continue;

            $quitValues=Quit::init();

            $payway=$this->payways->defaultQuitPayway();
            
            $quitValues['account_number'] = '01345679' . rand(100,100000);

            $random_key=array_rand($banks,1);
            $quitValues['account_bank'] = $banks[$random_key];
            $random_key=array_rand($branches,1);
            $quitValues['account_branch'] = $branches[$random_key];
            $quitValues['account_owner'] = $signup->user->profile->fullname;
            $quitValues['account_code'] = '98754' . rand(100,100000);
           
            $quitValues['paywayId']=$payway->id;

            $date=new Carbon('2018-4-2'); 
            $date= $date->addDays(rand(0 ,45));
            $quitValues['date']=$date;

            $quit=new Quit($quitValues);
            $quit=$this->quits->createQuit($signup, $quit,$quitDetails);
           
        }
        

        dd('done');
        
    }

    function readIndexRequest()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

      
        $status=-1;  
        if($request->exists('status'))  $status=(int)$request->status;

        
        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        if(!Quit::isValidStatus($status)) abort(404);
       
        $selectedCenter = null;
        if ($center)
        {
            $selectedCenter = Center::find($center);
            if (!$selectedCenter) abort(404);
            
        }

        $centerOptions = []; 
        $statusOptions = []; 
        if(!$this->isAjaxRequest()){
            $centerOptions = $this->centers->options($withEmpty=true);
            $statusOptions = $this->quits->statusOptions();
        }  

        $params=[
            'center' => $center,
            'status' => $status,
            'keyword' => $keyword,
           
            'page' => $page,
            'pageSize' => $pageSize

        ];

       
        return [
            'selectedCenter' => $selectedCenter,

            'params' => $params,

            'centerOptions' => $centerOptions,
            'statusOptions' => $statusOptions,
        ];
    }

    public function index()
    {
        $requestValues=$this->readIndexRequest();

        $canReview= $this->canReview();

        $selectedCenter=$requestValues['selectedCenter'];

        $params=$requestValues['params'];
        $status = $params['status'];
        $selectedPayway=null;

        $summary=$this->quits->getQuitSummary($selectedCenter,$selectedPayway,$status,$params['keyword']);
      
        $quits = $this->quits->fetchQuits($selectedCenter,$selectedPayway,$status,$params['keyword']);

       
       
        $pageList =$this->getPageList($quits, $params['page'],$params['pageSize']);
        
        if($this->isAjaxRequest()){
           
            $model=[
                'model' => $pageList,
                'summary' => $summary,
                'canReview' => $canReview
            ];
    
            return response()->json($model);
        }

        $centerOptions=$requestValues['centerOptions'];
        $statusOptions=$requestValues['statusOptions'];

        $model=[
            'title' => '退費管理',
            'menus' => $this->adminMenus('SignupsAdmin'),

            
            'centers' => $centerOptions,               
            'statuses' => $statusOptions,

            'canReview' => $canReview,
            'list' => $pageList,

            'summary' => $summary,

            'params' => $params
        ];

        return view('quits.index')->with($model);
           
    }

   

    function getPageList($quits,$page,$pageSize)
    {
        $quits = $quits->orderBy('date','desc');
        $pageList = new PagedList($quits,$page,$pageSize);
        
        foreach($pageList->viewList as $quit){
            $quit->loadViewModel();
        }  

        return $pageList;
    }

    

    function initQuitAccountValues(array $quitValues , User $user)
    {
        return $this->quits->initQuitAccountValues($quitValues , $user);
    }

    public function create()
    {
        $request=request();

        $signup=0;
        if($request->signup)  $signup=(int)$request->signup;

        $selectedSignup=$this->signups->getById($signup);
        if(!$selectedSignup) abort(404);

        $payway=$this->payways->defaultQuitPayway();

        $paywayOptions=[$payway->toOption()];

        $quit=Quit::init();
        $quit['paywayId'] = $payway->id;
        $quit['signupId'] = $selectedSignup->id;

        if($payway->needAccount()){
            $quit= $this->initQuitAccountValues( $quit ,$selectedSignup->user);
        }

       

        $quitDetails=[];
        foreach($selectedSignup->details as $signupDetail){
            if($signupDetail->canQuit()){
                $signupDetail->course->fullName();
                array_push($quitDetails, QuitDetail::init($signupDetail));
            }
        }
        
        
        $percentsOptions=$this->quits->percentsOptions();
        $form=[
            'quit' => $quit,
            'details' => $quitDetails,
            'paywayOptions' => $paywayOptions,
            'percentsOptions' => $percentsOptions
        ];

        return response()->json($form);
      
    }

    

    function validateQuitInputs(array $values,Payway $payway)
    {
        
        return $this->quits->validateQuitInputs($values,$payway);
    }

   

    public function store(QuitRequest $request)
    {
        $updatedBy=$this->currentUserId();
        $quitValues=$request->getQuitValues(); 

        $signup=$this->signups->getById($quitValues['signupId']);
        if(!$this->canQuits($signup)) return $this->unauthorized();
       
        $special=Helper::isTrue($quitValues['special']);
        $auto=false;

        $payway=Payway::findOrFail($quitValues['paywayId']);      

        $errors=$this->validateQuitInputs($quitValues,$payway);
       
        if($errors) return $this->requestError($errors);

        $quitDetails=$this->getQuitDetailsFromRequest($request,$special,$updatedBy);

        $quitValues['updatedBy']=$updatedBy;

        $userCanAddDetailQuit=$this->quits->getUserCanAddDetailQuit($signup->user,$auto,$special);
        if($userCanAddDetailQuit){
           
            $userCanAddDetailQuit->fill($quitValues);

            $this->quits->addQuitDetails($userCanAddDetailQuit,$quitDetails);
        }else{
            $quit=new Quit($quitValues);

            $quit=$this->quits->createQuit($signup, $quit,$quitDetails);
        };
        
        
        return response()->json();
       
    }

    function getQuitDetailsFromRequest(QuitRequest $request,$special,$updatedBy)
    {
        $detailsValues=$request->getQuitDetailValues();
       

        $quitDetails=[];
        foreach($detailsValues as $detail){
            $signupDetail=SignupDetail::findOrFail($detail['signupDetailId']);

            $percents=0;
            if($special)   $percents=(int)$detail['percents'];
            else $percents= $this->initQuitPercent($signupDetail->course);
           
            
            $actualTuition=$signupDetail->actualTuition();

            
            $tuition=round($actualTuition * $percents /100);
           
            $quitDetail=new QuitDetail([
                'signupDetailId' => $signupDetail->id,
                'percents' => $percents,
                'tuition' => $tuition,
                'updatedBy' => $updatedBy
            ]);
           
            array_push($quitDetails,$quitDetail);

           
        }

        return $quitDetails;
    }

    public function show($id)
    {
        $quit = Quit::findOrFail($id);

        $quit->signup->user->profile;
        $quit->loadViewModel();

        $quit->canEdit = $this->canEdit($quit);
        $quit->canDelete = $this->canDelete($quit);

        return response()->json($quit);
        
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

        $payway=$this->payways->defaultQuitPayway();

        $paywayOptions=[$payway->toOption()];
      
        $form=[
            'quit' => $quit,
            'details' => $quit->details,
            'paywayOptions' => $paywayOptions,
            'percentsOptions' => $percentsOptions
        ];

        return response()->json($form);
       
        
    }

    public function update(QuitRequest $request, $id)
    {
        $quit = $this->quits->getById($id);   
        
        if(!$quit) abort(404);   
        if(!$this->canEdit($quit)) $this->unauthorized();

        $updatedBy=$this->currentUserId();
        $quitValues=$request->getQuitValues(); 
       
        $special=Helper::isTrue($quitValues['special']);

        $payway=Payway::findOrFail($quitValues['paywayId']);      

        $errors=$this->validateQuitInputs($quitValues,$payway);
       
        if($errors) return $this->requestError($errors);




        $quitDetails=$this->getQuitDetailsFromRequest($request,$special,$updatedBy);

        $quitValues['updatedBy']=$updatedBy;

        
        $quit->fill($quitValues);

        $quit=$this->quits->updateQuit($quit,$quitDetails);
        
        return response()->json();
       
    }

    public function updateStatuses(Request $form)
    {
        
        if(!$this->canReview()) return $this->unauthorized();

        $updatedBy=$this->currentUserId();

        $errors=[];

        $status=(int)$form['status'];
        if($status==-1) $errors =$this->quits->setUnHandled($updatedBy);
        else if($status==1) $errors =$this->quits->reviewOK($updatedBy);
        else if($status==2) $errors =$this->quits->finishOK($updatedBy);

        if($errors) return $this->requestError($errors);

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
        

        return response()->json();


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
