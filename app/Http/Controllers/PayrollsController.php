<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PayrollRequest;

use App\Course;
use App\ClassTime;
use App\Center;
use App\Profile;
use App\Payroll;
use App\PayrollMember;
use App\Weekday;
use App\Services\Users;
use App\Services\Terms;
use App\Services\Centers;
use App\Services\Teachers;
use App\Services\Volunteers;
use App\Services\Courses;
use App\Services\CourseInfoes;
use App\Services\Payrolls;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;
use Exception;

class PayrollsController extends Controller
{
    
    public function __construct(Terms $terms,Centers $centers,Courses $courses,CourseInfoes $courseInfoes,Payrolls $payrolls,
    Users $users,Teachers $teachers, Volunteers $volunteers)
    {
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->courseInfoes=$courseInfoes;
        $this->payrolls=$payrolls;
        $this->users=$users;
        $this->teachers=$teachers;
        $this->volunteers=$volunteers;
      
    }

    public function test()
    {
        $center=$this->centers->getCenterByCode('A');
       
        $this->payrolls->initPayrolls($center , 107, 4);
    }

    function canFinish(Payroll $payroll)
    {
        return $this->canAdminCenter($payroll->center);

    }

    function canReview(Payroll $payroll)
    {
        return $this->canReviewCenter($payroll->center);
    }

    function canDelete(Payroll $payroll)
    {
        if($payroll->reviewed) return false;
        return $this->canReviewCenter($payroll->center);
    }

    function canReviewCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canAdminCenter($center);
    }
    
    function yearOptions()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);
        $min=$thisYear - 5;
        $max=$thisYear + 1;
        $options=[];
        for ($i = $max; $i >= $min; $i--)
        {
            $item=[ 'text' => $i . '年' ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }

    function monthOptions()
    {
        $options=[];
        for ($i = 1; $i <= 12; $i++)
        {
            $item=[ 'text' => $i . '月' ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    

    public function loadViewModel(Payroll $payroll)
    {
        $payroll->loadViewModel();
    }

    public function index()
    {
        $request=request();

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $year=0;
        if($request->year)  $year=(int)$request->year;

        $month=0;
        if($request->month)  $month=(int)$request->month;

        $reviewed=false;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);
        
       
        if($this->isAjaxRequest()){
            return $this->fetchPayrolls($center, $year, $month, $reviewed);
        }

        
        $centerOptions = $this->centers->options();

        $selectedCenter = null;
        if ($center)
        {
            $selectedCenter = $this->centers->getById($center);
            if (!$selectedCenter) abort(404);
        }
        else
        {
            $selectedCenter = $this->centers->getById($centerOptions[0]['value']); 
        }

        $yearOptions=$this->yearOptions();
        $selectedYear = 0;
        if ($year)
        {
            foreach($yearOptions as $item) {
                if((int)$item['value']==$year){
                    $selectedYear=(int)$item['value'];
                    break;
                }
            }
        }

        if(!$selectedYear) $selectedYear=Carbon::today()->year -1911;

        $monthOptions=$this->monthOptions();
        $selectedMonth = 0;
        if ($month)
        {
            foreach($monthOptions as $item) {
                if((int)$item['value']==$month){
                    $selectedMonth=(int)$item['value'];
                    break;
                }
            }
        }

        if(!$selectedMonth) $selectedMonth=Carbon::today()->month;
        

        $payrolls = $this->payrolls->fetchPayrolls($selectedCenter, $selectedYear,$selectedMonth);
        
        $payrolls = $payrolls->where('reviewed',$reviewed);

        $pageList =$this->getPageList($payrolls);

        

        $canReview=$this->canReviewCenter($selectedCenter);

        
        $model=[
            'title' => '教師鐘點費',
            'menus' => $this->adminMenus('CoursesAdmin'),

            
            'centers' => $centerOptions,
            'years' => $yearOptions,  
            'months' => $monthOptions,  
            
            'year' => $selectedYear,  
            'month' => $selectedMonth,  

            'canReview' => $canReview,
            'canFinish' => $this->canAdminCenter($selectedCenter),
           
            'list' => $pageList
        ];

        return view('payrolls.index')->with($model);
           
    }

    function getPageList($payrolls)
    {
        
        $pageList = new PagedList($payrolls);
       
        foreach($pageList->viewList as $payroll){
            $this->loadViewModel($payroll);
        }  

        return $pageList;
    }

    //Ajax
    function fetchPayrolls(int $center = 0, int $year = 0, int $month = 0, $reviewed)
    {
       
        $selectedCenter = null;

        if ($center)
        {
            $selectedCenter = $this->centers->getById($center);
            if (!$selectedCenter) abort(404);
        }

        if(!$year)   $year=Carbon::today()->year -1911;
        if(!$month)  $month=Carbon::today()->month;

        $payrolls = $this->payrolls->fetchPayrolls($selectedCenter, $year,$month);
        
        $payrolls = $payrolls->where('reviewed',$reviewed);

        $pageList =$this->getPageList($payrolls);

        $canReview=$this->canReviewCenter($selectedCenter);

        $model=[
            'canReview' => $canReview,
            'model' => $pageList
        ];

        return response() ->json($model);

        
    }

    
    public function store(Request $form)
    {
        $year=  $form['year'];
        $month=  $form['month'];
        $center=  $form['center'];

        $selectedCenter=$this->centers->getById($center);
        if(!$selectedCenter) abort(404);

        if(!$this->canAdminCenter($selectedCenter))  return $this->unauthorized();

       

        $errors=[];
        $exist=$this->payrolls->fetchPayrolls($selectedCenter,$year,$month)
                              ->where('reviewed',true)->first();
                             

        if($exist) $errors['payroll.duplicate'] = ['相同月份的鐘點費已經存在'];

        if($errors) return $this->requestError($errors);

        
        $this->payrolls->initPayrolls($selectedCenter,$year,$month);

        return response()->json();


    }

    public function show($id)
    {
        $payroll = $this->payrolls->getById($id);
        if(!$payroll) abort(404);

        $this->loadViewModel($payroll);

        foreach($payroll->details as $detail){
            $detail->loadViewModel();
        }

      
        $payroll->canDelete = $this->canDelete($payroll);
        $payroll->canReview = $this->canReview($payroll);

        return response()->json($payroll);
        
    }

    
    function validateInputs(array $values ,array $teacherIds)
    {
        $errors=[];

        $on=$values['on'];
        $off=$values['off'];
        
        $on=(int)$on;
        if(!$this->courseInfoes->isValidTimeNumber($on)){
            $errors['payroll.on'] = ['時間錯誤'];
        }
        $off=(int)$off;
        if(!$this->courseInfoes->isValidTimeNumber($off)){
            $errors['payroll.off'] = ['時間錯誤'];
        }

        if($on >= $off)  $errors['payroll.off'] = ['時間錯誤'];
       

        if(!count($teacherIds))  $errors['teacherIds'] = ['請選擇教師'];

        return $errors;
    }

    

    


    public function review(Request $form)
    {
        $payrolls= $form['payrolls'];

        $id=$payrolls[0]['id'];
        $payroll=$this->payrolls->getById($id);

        if(!$payroll) abort(404);   
        if(!$this->canReview($payroll)) return $this->unauthorized();
        

        $reviewedBy=$this->currentUserId();

        if(count($payrolls) > 1){
            $payrollIds=array_pluck($payrolls, 'id');
            $this->payrolls->reviewOK($payrollIds, $reviewedBy);
        }else{
            
            $id=$payrolls[0]['id'];
         
            $reviewed=Helper::isTrue($payrolls[0]['reviewed']);

            $this->payrolls->updateReview($id,$reviewed ,$reviewedBy);
        }

        return response() ->json();


    }

    public function finish(Request $form)
    {
        $updatedBy=$this->currentUserId();
        
        $payrolls=  $form['payrolls'];

        if(count($payrolls) > 1){
            $payrollIds=array_pluck($payrolls, 'id');

            $payroll = $this->payrolls->getById($payrollIds[0]); 
            if(!$this->canFinish($payroll)) return $this->unauthorized();
           
            $this->payrolls->finishOK($payrollIds, $updatedBy);

        }else{
            
            $id=$payrolls[0]['id'];

            $payroll = $this->payrolls->getById($id); 
            if(!$this->canFinish($payroll)) return $this->unauthorized();
         
            $finish=Helper::isTrue($payrolls[0]['finish']);

            $this->payrolls->updateFinish($id, $finish ,$updatedBy);
        }
        

        return response() ->json();


    }

    public function updatePS(Request $form)
    {
        $id=$form['id'];
        $ps=$form['ps'];

        $payroll = $this->payrolls->getById($id);   
        if(!$payroll) abort(404);   

        if(!$this->canAdminCenter($payroll->center)) return $this->unauthorized();
        
        
        $payroll->update([
             'ps' => $ps,
             'updatedBy' => $this->currentUserId()
        ]);
        

        return response() ->json();


    }

    public function updateStatus(Request $form)
    {
        $id=$form['id'];
        $status=$form['status'];

        $payroll = $this->payrolls->getById($id);   
        if(!$payroll) abort(404);   

        $updatedBy=$this->currentUserId();

        if(!$this->canAdminCenter($payroll->center)) return $this->unauthorized();
        if(Helper::isTrue($status)){
            $this->payrolls->finishOK([$id],  $updatedBy);
        }else{
            $payroll->update([
                'status' => $status,
                'updatedBy' => $updatedBy
           ]);
        }
       
        
        
        

        return response() ->json();


    }

    public function destroy($id) 
    {
        $payroll=Payroll::findOrFail($id);
        $payroll->delete();
       
       
        return response() ->json();
    }

    
}
