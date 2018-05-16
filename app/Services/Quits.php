<?php

namespace App\Services;
use App\User;
use App\Signup;
use App\SignupDetail;
use App\Student;
use App\Profile;
use App\Center;
use App\Quit;
use App\QuitDetail;
use App\Course;
use App\Payway;
use App\Services\Payways;
use DB;
use Carbon\Carbon;

class Quits 
{
    public function __construct(Payways $payways)
    {
        $this->payways=$payways;

        $this->statuses=array(
            ['value'=> -1 , 'text' => '待處理'],
            ['value'=> 0 , 'text' => '審核中'],
            ['value'=> 1 , 'text' => '已審核'],
            ['value'=> 2 , 'text' => '已完成'],
        );

        $this->with=['signup.user.profile','details','payway'];
    }

    public function percentsOptions(bool $withEmpty=true)
    {
        $options = array(
            ['value'=> 90 , 'text' => '9成'],
            ['value'=> 70 , 'text' => '7成'],
            ['value'=> 50 , 'text' => '5成']
        );

        if($withEmpty) array_unshift($options, ['text' => '-------' , 'value' => 0 ]);
        
        return $options;
    }

    public function getAll()
    {
        return Quit::with($this->with); 
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('signupId', $ids);
    }
    
    public function getById($id)
    {   
        return Quit::with($this->with)->find($id);
    }
    public function fetchQuits(Center $center=null,Payway $payway=null,int $status)
    {
      
        $quits = [];
        if($center) $quits = $this->fetchQuitsByCenter($center);
        else $quits = $this->getAll();

        if($payway) $quits = $quits->where('paywayId' , $payway->id);

        return $quits->where('status' , $status);
    }

    public function fetchQuitsByCenter(Center $center)
    {
        $courseIds=Course::where('removed',false)->where('centerId',$center->id)
                                                 ->pluck('id')->toArray();    
                                            
        $signupDetails=SignupDetail::whereIn('courseId',$courseIds);
        $signupIds=array_unique($signupDetails->pluck('signupId')->toArray());

        return $this->getByIds($signupIds);

    }

    public function createQuitsByCourse(Course $course, $percents)
    {
        //為每個學生產生退費
        $studentsInCourse=Student::where('courseId',$course->id);
        $studntUserIds= $studentsInCourse->pluck('userId')->toArray();
        
        $signups=Signup::whereIn('userId',$studntUserIds)->get();
        $date=Carbon::today()->toDateString();  

        foreach($signups as $signup){
            if($signup->quit) continue;  //之前已經退費

            $signupDetail=$signup->details()->where('courseId',$course->id)->first();
            $actualTuition=$signupDetail->actualTuition();

            $tuition=round($actualTuition * $percents /100);

            $quitDetail=new QuitDetail([
                'signupDetailId' => $signupDetail->id,
                'percents' => $percents ,   
                'tuition' => $tuition,
            ]);

            $payway=$this->payways->initQuitPaywayBySignup($signup);
              
            $quit=new Quit([
                'date' =>$date,
                'tuitions' => $tuition,
                'fee' => 0, // 手續費
                'paywayId' => $payway->id,
                'auto' => true,  
                'ps' =>  $date . '課程停開'
            ]);

            DB::transaction(function() use($signup,$quit,$quitDetail) {
            
                $signup->quit()->save($quit);
            
                $quit=Quit::find($signup->id);
            
                $quit->details()->save($quitDetail);
               
                $signup->update([
                    'status' => -1,
                   
                ]);
                
            });
            
        }
        foreach($studentsInCourse->get() as $student){
            $student->update([
                'status' => -1, 
                'quitDate' => $date
            ]);
        }
        
    }

    public function createQuit(Signup $signup,Quit $quit, array $quitDetails)
    {
        $quit = DB::transaction(function() use($signup,$quit,$quitDetails) {
            
            $tuitions = 0;
            foreach($quitDetails as $quitDetail) {
                $tuitions += $quitDetail['tuition'];
            }

            $quit->tuitions=$tuitions;
            $quit->fee=$this->countFee($quit->paywayId,$tuitions);
          
            $signup->quit()->save($quit);

           
            
            $quit=Quit::find($signup->id);
           
            $quit->details()->saveMany($quitDetails);

           
            $signup->update([
                'status' => -1
            ]);

            $signupDetailIds=collect($quitDetails)->pluck('signupDetailId')->toArray();
            
            foreach($signup->details as $signupDetail){
                if(in_array($signupDetail->id, $signupDetailIds)){
                    $student= $signupDetail->getStudent();
                    $student->update([
                        'status' => -1, 
                        'quitDate' => $quit->date
                    ]);
                } 
               
            }
            
            return $quit;
		});
    

        return $quit;
    }

    function countFee($paywayId, $amount)
    {
       
        return  Payway::find($paywayId)->getFee($amount);
    }

    public function updateQuit(Quit $quit ,array $quitValues, array $detailsValues)
    {
        $date=$quitValues['date'];
        DB::transaction(function() use($quit,$quitValues,$detailsValues) {
           
            foreach($detailsValues as $detail){
                $quitDetail=QuitDetail::find($detail['id']);

                $percents=(int)$detail['percents'];
               
                if(!$percents){
                    $signupDetail=SignupDetail::find($detail['signupDetailId']);
                    $student= $signupDetail->getStudent();
                    $student->update([
                        'status' => 1, 
                        'quitDate' => $date
                    ]);
                    $quitDetail->delete();
                }else{
                    $quitDetail->update($detail);
                } 
            }

            $quitDetails=QuitDetail::where('signupId',$quit->signupId)->get();
            $tuitions = 0;
            foreach($quitDetails as $quitDetail) {
                $tuitions += $quitDetail->tuition;
            }

            $quitValues['tuitions']=$tuitions;
            $quitValues['fee']=$this->countFee($quitValues['paywayId'],$tuitions);
            $quit->update($quitValues);

            
           
		});
    

       
    }

    

    public function reviewOK(array $ids, $reviewedBy)
    {
        $quits=$this->getByIds($ids)->get();
        foreach($quits as $quit){
            $quit->status=1;
            $quit->reviewedBy=$reviewedBy;
            $quit->updatedBy=$reviewedBy;
            $quit->save();
        } 
    }

    public function finishOK(array $ids,  $updatedBy)
    {
        $quits=$this->getByIds($ids)->get();
        foreach($quits as $quit){
            if(!$quit->status==1) abort(500);
            $quit->status=2;
            $quit->updatedBy=$updatedBy;
            $quit->save();
        } 
    }

    public function  updateFinish($id, bool $finish, $updatedBy)
    {
        $quit=Quit::findOrFail($id);

        if($finish){
            $quit->status=1;
            if(!$quit->reviewed) abort(500);
           
        }else{
            $quit->status=0;
        }
        
        $quit->updatedBy=$updatedBy;
        

        $quit->save();
    }

    public function  updateReview($id,bool $reviewed,int $reviewedBy)
    {
        $quit=Quit::findOrFail($id);
       
        $quit->updatedBy=$reviewedBy;

        if($reviewed){
            $quit->status=1;
        }else{
            $quit->status=0;
        }

        $quit->save();
    }

    public function deleteQuit(Quit $quit , $updatedBy)
    {
        DB::transaction(function() use($quit,$updatedBy) {
           
            foreach($quit->details as $detail){
                $signupDetail=SignupDetail::find($detail->signupDetailId);
                    
                $student= $signupDetail->getStudent();
                $student->update([
                    'status' => 1, 
                    'quitDate' => '',
                    'updatedBy' => $updatedBy,
                ]);
            }

            $quit->signup->update([
                'status' => 1,
                'updatedBy' => $updatedBy
            ]);

            $quit->delete();
           
		});
    

       
    }

    
    public function statusOptions()
    {
        return $this->statuses;
    }
    
   

    
    
}