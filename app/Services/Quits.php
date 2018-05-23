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
use App\Services\Signups;
use DB;
use Carbon\Carbon;

class Quits 
{
    public function __construct(Payways $payways,Signups $signups)
    {
        
        $this->payways=$payways;
        $this->signups=$signups;

        $this->statuses=Quit::statuses();

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
        return $this->getAll()->whereIn('id', $ids);
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

    public function getUserCanAddDetailQuit(User $user)
    {
                                
        $signupIds=$this->signups->fetchSignupsByUser($user)->pluck('id')->toArray();

        $userQuits=$this->getAll()->whereIn('signupId',$signupIds)->get();
       
        
        $userQuits = $userQuits->filter(function ($quit) {
            return $quit->canAddDetail();
        });
        
        return  $userQuits->first();
    }

    public function undoCreateQuitsByCourse(Course $course)
    {

    }

    public function createQuitsByCourse(Course $course, $percents)
    {
        
        //為每個"Active"學生產生退費
        $studentsInCourse=Student::where('courseId',$course->id)->get();
        $activeStudents = $studentsInCourse->filter(function ($student) {
            return !$student->hasQuit();
        });

        $studntUserIds= $activeStudents->pluck('userId')->toArray();
       
        
        $signups=Signup::whereIn('userId',$studntUserIds)->get();
       
        $date=Carbon::today()->toDateString();  

        foreach($signups as $signup){
            $signupDetail=$signup->details()->where('courseId',$course->id)->first();
            if($signupDetail->canceled) continue;

            $actualTuition=$signupDetail->actualTuition();

            $tuition=round($actualTuition * $percents /100);

            $quitDetail=new QuitDetail([
                'signupDetailId' => $signupDetail->id,
                'percents' => $percents ,   
                'tuition' => $tuition,
            ]);

            $userCanAddDetailQuit=$this->getUserCanAddDetailQuit($signup->user);
            if($userCanAddDetailSignup){

            }

            $payway=$this->payways->initQuitPaywayBySignup($signup);
              
            $quit=new Quit([
                'date' =>$date,
                'tuitions' => $tuition,
                'fee' => 0, // 手續費
                'paywayId' => $payway->id,
                'auto' => true,  
               
            ]);

            $this->createQuit($signup,$quit, [$quitDetail]);
            
        }
        
        
    }

    public function createQuit(Signup $signup,Quit $quit, array $quitDetails)
    {
       
        $quit = DB::transaction(function() use($signup,$quit,$quitDetails) {
            
            $signup->quits()->save($quit);
           
            $quit->details()->saveMany($quitDetails);

            $quit=Quit::find($quit->id);

            $quit->updateMoney();

            $signup=Signup::find($quit->signupId);
            $signup->updateStatus();
           
            

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

    public function updateQuit(Quit $quit, array $details=[])
    {
       
        DB::transaction(function() use($quit,$details) {
            $quit->save();

            foreach($details as $detail){
                $exist=$quit->details()->where('signupDetailId',$detail['signupDetailId'])
                                        ->first();
                if($exist){
                    $exist->update($detail->toArray());
                }else{
                    $quit->details()->save($detail);
                }                        
            }

            $quit=Quit::find($quit->id);
            $quit->updateMoney();

            $signup=Signup::find($quit->signupId);
            $signup->updateStatus();
        });

       
        
    }

    public function addQuitDetails(Quit $quit, array $newDetails=[])
    {
       
        DB::transaction(function() use($quit,$newDetails) {
            $quit->save();
            $quit->details()->saveMany($newDetails);

            $quit=Quit::find($quit->id);
            $quit->updateMoney();
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

            $signupId=$quit->signupId;

            $quit->delete();

            $signup=Signup::find($quit->signupId);
            $signup->updateStatus();
           
           
		});
    

       
    }

    
    public function statusOptions()
    {
        return $this->statuses;
    }
    
   

    
    
}