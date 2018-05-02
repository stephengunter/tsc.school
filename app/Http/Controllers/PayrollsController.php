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
        for ($i = 1; $i <= 12; $i++)
        {
            $item=[ 'text' => $i . '月' ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    

    public function loadViewModel(Payroll $payroll)
    {
        $teacherIds=$payroll->getTeacherIds();
        $payroll->teachers=$this->users->getByIds($teacherIds)->get();

        $volunteerIds=$payroll->getVolunteerIds();
        $payroll->volunteers=$this->users->getByIds($volunteerIds)->get();

        $studentIds=$payroll->getStudentIds();
        $payroll->students=$this->users->getByIds($studentIds)->get();

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

        if(!$selectedYear) $selectedYear=Carbon::today()->year;

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
           
            'list' => $pageList
        ];

        return view('payrolls.index')->with($model);
           
    }

    function getPageList($payrolls)
    {
        $payrolls=$payrolls->orderBy('date');
        $pageList = new PagedList($payrolls);
       
        foreach($pageList->viewList as $payroll){
            $this->loadViewModel($payroll);
        }  

        return $pageList;
    }

    //Ajax
    function fetchPayrolls(int $term = 0, int $center = 0, int $course = 0,$beginDate,$endDate, $reviewed)
    {
       
        $selectedCenter = null;
        $selectedTerm = null;
        $selectedCourse = null;

        if ($course)
        {
        
            $selectedCourse = $this->courses->getById($course);
            
            if (!$selectedCourse) abort(404);
            else
            {
                $selectedCenter = $selectedCourse->center;
                $selectedTerm = $selectedCourse->term;
            }

        }
        else
        {
            if ($center)
            {
                $selectedCenter = $this->centers->getById($center);
                if (!$selectedCenter) abort(404);
            }


            $selectedTerm =  $this->terms->getById($term);
            if (!$selectedTerm) return abort(404);

        }

        $payrolls = $this->payrolls->fetchPayrolls($selectedTerm, $selectedCenter, $selectedCourse,$beginDate,$endDate);
        
        $payrolls = $payrolls->where('reviewed',$reviewed);
        
        $pageList =$this->getPageList($payrolls);

        

        $courseOptions=$this->courses->options($selectedTerm,$selectedCenter,true);

        $model=[
            'courseOptions' => $courseOptions,
            'model' => $pageList
        ];

        return response() ->json($model);

        
    }

    public function show($id)
    {
        $payroll = $this->payrolls->getById($id);
        if(!$payroll) abort(404);

        $this->loadViewModel($payroll);

        foreach($payroll->members as $member){
            $member->name=Profile::find($member->userId)->fullname;
        }

        $payroll->canEdit = $this->canEdit($payroll);
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

    public function edit($id)
    {
        $payroll = $this->payrolls->getById($id);
        if(!$payroll) abort(404);

        if(!$this->canEdit($payroll))  return $this->unauthorized();
        

        $teacherIds=$this->getTeacherIds($payroll);
       
        $volunteerIds=$this->getVolunteerIds($payroll);
       
        $teacherOptions = $this->teacherOptions($payroll);
        
        $volunteerOptions = $this->volunteerOptions($payroll);

        $payroll->course->fullName();

        $form=[
            'payroll' => $payroll,
            'teacherIds' =>$teacherIds,
            'volunteerIds' =>$volunteerIds,
            'teacherOptions' => $teacherOptions,
            'volunteerOptions' => $volunteerOptions,
        ];

        return response() ->json($form);
        
    }

    public function update(PayrollRequest $request, $id)
    {
        $payroll = $this->payrolls->getById($id); 
        if(!$payroll) abort(404);   
        if(!$this->canEdit($payroll)) return $this->unauthorized();

        $payrollValues=$request->getValues();
       
        $teacherIdValues=$request->getTeacherIds();
        $volunteerIdValues=$request->getVolunteerIds();
       
        $errors=$this->validateInputs($payrollValues,$teacherIdValues);
        if($errors) return $this->requestError($errors);


        $courseId=$payroll->courseId;
        $classTime=new ClassTime([
            'on' => $payrollValues['on'],
            'off' => $payrollValues['off']
        ]);


        $date=new Carbon($payrollValues['date']);
        $exist=$this->payrolls->findByCourseDateTime($courseId,$classTime,$date);
        
        if($exist && $exist->id != $payroll->id){
            $errors['payroll.date'] = ['日期時間與其他課堂重覆了'];            
        }

        if($errors) return $this->requestError($errors);

        $teacherIds = [];
        foreach($teacherIdValues as $item){
            array_push($teacherIds,$item['value']);
        }

        $volunteerIds = [];
        foreach($volunteerIdValues as $item){
            array_push($volunteerIds,$item['value']);
        }


        $payrollValues['updatedBy']=$this->currentUserId();
        $payroll->fill($payrollValues);

        $this->payrolls->updatePayroll($payroll, $teacherIds,$volunteerIds);

        return response()->json();
    }

    public function updateMember(Request $request)
    {
        $id=$request['id'];
        $member=PayrollMember::findOrFail($id);
        
        $absence=$request['absence'];
        $ps=$request['ps'];

        $member->absence=$absence;
        $member->ps=$ps;
        $member->updatedBy=$this->currentUserId();

        $member->save();
        
        return response() ->json();
    }

    public function init(Request $form)
    {
        $date=  $form['date'];
        $center=  $form['center'];

        $selectedCenter=$this->centers->getById($center);
        if(!$selectedCenter) abort(404);

        if(!$this->canAdminCenter($selectedCenter))  return $this->unauthorized();

       
        $this->payrolls->initPayrollsByDate(new Carbon($date));

        return response() ->json();


    }

    public function review(Request $form)
    {
        $reviewedBy=$this->currentUserId();
        
        $payrolls=  $form['payrolls'];

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

    public function destroy($id) 
    {
        $payroll=Payroll::findOrFail($id);
        $payroll->delete();
       
       
        return response() ->json();
    }

    
}
