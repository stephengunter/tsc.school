<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Course;
use App\Term;
use App\Center;
use App\ClassTime;
use App\Lesson;
use App\Payroll;
use App\PayrollDetail;
use App\Weekday;
use App\Services\Courses;
use App\Services\Terms;
use App\Services\Lessons;
use App\Services\Teachers;
use DB;
use Carbon\Carbon;
use App\Core\PagedList;
use Excel;

class Payrolls 
{
    public function __construct(Terms $terms,Courses $courses,Lessons $lessons,Teachers $teachers)
    {
        $this->terms=$terms;
        $this->courses=$courses;
        $this->lessons=$lessons;
        $this->teachers=$teachers;
        $this->with=['details','user.profile'];
    }
    public function getAll()
    {
        return Payroll::with($this->with); 
    }
    public function getById($id)
    {   
        return Payroll::with($this->with)->find($id);
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id', $ids);
    }

    public function initPayrolls(Center $center , $year, $month)
    {
        $existRecordIds=$this->fetchPayrolls($center,$year,$month)->pluck('id')->toArray();
        Payroll::destroy($existRecordIds);

        $year=(int)$year + 1911;
        //找出課堂紀錄
        $lessons=$this->lessons->getByCenter($center);
        $lessons=$lessons->whereYear('date',$year)->whereMonth('date',$month)
                         ->where('reviewed',true)->get();
        
        // 計算教師的鐘點費標準
       
        $teacherIds=[];
        $payrollDetails=[];
        foreach($lessons as $lesson){
            
            $result=$this->initPayrollDetailsByLesson($lesson);
            
            $teacherIds = array_merge($teacherIds,$result['teacherIds']);
            $payrollDetails = array_merge($payrollDetails,$result['payrollDetails']);
            
        }

        $payrollDetails=collect($payrollDetails);
       
        $teacherIds=array_unique($teacherIds);
       
        foreach($teacherIds as $teacherId){
            $teacher=$this->teachers->getById($teacherId);
            $payroll=new Payroll([
                    'centerId' => $center->id,
                    'userId' => $teacherId,
                    'year' => $year - 1911,
                    'month' => $month,
                    'wageName' => $teacher->wage->name,
                  ]);
           
              
            
            $details = $payrollDetails->where('teacherId',$teacherId)->map(function ($item) {
                return new PayrollDetail($item);
            })->all();

            DB::transaction(function() use($payroll,$details) {
                $payroll->save();
                $payroll->details()->saveMany($details);
            });
            
           
            
        }
        
       

    }


    function initPayrollDetailsByLesson(Lesson $lesson)
    {
        $teacherIds=$lesson->getTeacherIds();
        $teachers=$this->teachers->getByIds($teacherIds)->get();

        $payrollDetails=[];
        foreach($teachers as $teacher){
            $actualMoney=$teacher->wage->getActualMoney($lesson);
            if(!$actualMoney) $actualMoney=$teacher->pay;
            if(!$actualMoney) continue;

            $detail=[
                'teacherId' => $teacher->userId,
                'lessonId' => $lesson->id,
                'date' => $lesson->date,
                'on' => $lesson->on,
                'off' => $lesson->off,
                'minutes' => $lesson->getMinutes(),
                'studentCount' => $lesson->getStudentCount(),
                
                'wageMoney' => $actualMoney
            ];

            array_push($payrollDetails,$detail);
        }

        return [
            'teacherIds' => $teacherIds,
            'payrollDetails' => $payrollDetails
        ];

    }
   

   

    

    public function  updatePayroll(Payroll $payroll ,Array $teacherIds=[],Array $volunteerIds=[])
    {
        $payroll->deleteMembersByRole(Role::teacherRoleName());
        $payroll->deleteMembersByRole(Role::volunteerRoleName());

        $members=[];
        foreach($teacherIds as $teacherId){
            array_push($members,new PayrollMember([
                'userId' => $teacherId,
                'role' => Role::teacherRoleName(),

            ]));
        }
        foreach($volunteerIds as $volunteerId){
            array_push($members,new PayrollMember([
                'userId' => $volunteerId,
                'role' => Role::volunteerRoleName(),

            ]));
        }

        DB::transaction(function() use($payroll,$members) {
            
            $payroll->save();
            $payroll->members()->saveMany($members);
        });
        
        
    }

    public function fetchPayrollsByCenter(Center $center)
    {
        return $this->getAll()->where('centerId',$center->id);

    }

    public function findByYearMonth($query, $year, $month)
    {
        return $query->where('year',$year)->where('month',$month);
    }
    
    public function fetchPayrolls(Center $center, $year, $month)
    {
        $payrolls=$this->fetchPayrollsByCenter($center);
        $payrolls=$this->findByYearMonth($payrolls,$year, $month);

        return $payrolls;

    }

    

    
    
    
    public function reviewOK(array $ids, $reviewedBy)
    {
        $payrolls=Payroll::whereIn('id',$ids)->get();
        foreach($payrolls as $payroll){
            $payroll->reviewed=true;
            $payroll->reviewedBy=$reviewedBy;
            $payroll->updatedBy=$reviewedBy;
            $payroll->save();
        } 
    }

    public function  updateReview($id,bool $reviewed,int $reviewedBy)
    {
        $payroll=Payroll::find($id);
        $payroll->reviewed=$reviewed;
        $payroll->updatedBy=$reviewedBy;

        if($reviewed){
            $payroll->reviewedBy=$reviewedBy;
        }else{
            $payroll->reviewedBy='';
        }
        $payroll->save();
    }

    public function finishOK(array $ids,  $updatedBy)
    {
        $payrolls=$this->getByIds($ids)->get();
        foreach($payrolls as $payroll){
            if(!$payroll->reviewed) abort(500);
            $payroll->status=1;
            $payroll->updatedBy=$updatedBy;
            $payroll->save();
        } 
    }

    public function  updateFinish($id, bool $finish, $updatedBy)
    {
        $payroll=Payroll::findOrFail($id);

        if($finish){
            $payroll->status=1;
            if(!$payroll->reviewed) abort(500);
           
        }else{
            $payroll->status=0;
        }
        
        $payroll->updatedBy=$updatedBy;
        

        $payroll->save();
    }
   
    
}