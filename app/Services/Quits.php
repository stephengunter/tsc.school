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
use App\Services\Users;
use DB;
use Carbon\Carbon;

class Quits 
{
    public function __construct(Payways $payways,Users $users,Signups $signups)
    {
        
        $this->payways=$payways;
        $this->signups=$signups;
        $this->users=$users;
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

    public function getQuitSummary(Center $center=null,Payway $payway=null,int $status,$keyword='')
    {
        $quits=$this->fetchQuits($center,$payway,$status,$keyword)->get();
        $amount=0;
        foreach($quits as $quit){
            $amount += $quit->amount();
        }
        
        return [
            'count' => $quits->count(),
            'amount' => $amount
        ];

    } 


    public function fetchQuits(Center $center=null,Payway $payway=null,int $status,$keyword='')
    {
      
        $quits = [];
        if($center) $quits = $this->fetchQuitsByCenter($center);
        else $quits = $this->getAll();

        if($payway) $quits = $quits->where('paywayId' , $payway->id);

        $quits = $quits->where('status' , $status);

        if($keyword)   $quits =$this->filterQuitsByKeyword($quits, $keyword);

        return $quits;
    }

    function filterQuitsByKeyword($quits, $keyword)
    {
        $userIds=$this->users->getByKeyword($keyword)->pluck('id')->toArray();
       
        $signupIds= Signup::whereIn('userId',$userIds)->pluck('id')->toArray();
        return $quits->whereIn('signupId',$signupIds);
    }

    public function fetchQuitsByCenter(Center $center)
    {
        $courseIds=Course::where('removed',false)->where('centerId',$center->id)
                                                 ->pluck('id')->toArray();    
                                            
        $signupDetails=SignupDetail::whereIn('courseId',$courseIds);
        $signupIds=array_unique($signupDetails->pluck('signupId')->toArray());

        return $this->getByIds($signupIds);

    }

    public function getByUser(User $user)
    {
        $userSignupIds=$this->signups->fetchSignupsByUser($user)->pluck('id')->toArray();

        return $this->getAll()->whereIn('signupId',$userSignupIds);
       
    }

    public function getByStatus($status)
    {
       
        return $this->getAll()->where('status',$status);
       
    }

    public function getUserCanAddDetailQuit(User $user,bool $auto,bool $special)
    {
        $userQuitRecords=$this->getByUser($user)
                            ->where('auto',$auto)->where('special',$special)
                            ->orderBy('date','desc')->get();
        
        $userQuits = $userQuitRecords->filter(function ($quit) {
            return $quit->canAddDetail();
        });
        
        return  $userQuits->first();
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

        $auto=true;
        $special=false;

        $payway=$this->payways->defaultQuitPayway();

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

            $quitDetails=[$quitDetail];

            $quitValues=[
                'date' =>$date,          
            ];

            $userCanAddDetailQuit=$this->getUserCanAddDetailQuit($signup->user,$auto,$special);
            
            if($userCanAddDetailQuit){
           
                $userCanAddDetailQuit->fill($quitValues);
    
                $this->addQuitDetails($userCanAddDetailQuit,$quitDetails);

            }else{
                $quitValues['paywayId'] = $payway->id;
                $quitValues['special'] = $special;
                $quitValues['auto'] = $auto;
                $quit=new Quit($quitValues);
    
                $this->createQuit($signup, $quit,$quitDetails);
            };

           
            
        }
        
        
    }

    public function setStudentsQuit(Quit $quit)
    {
        foreach($quit->details as $quitDetail){

            $signupDetail=$quitDetail->getSignupDetail();
            $student= $signupDetail->getStudent();
            $student->update([
                'status' => -1, 
                'quitDate' => $quit->date
            ]);
            
        }
    }

    public function  initQuitAccountValues(array $quitValues , User $user)
    {
        $userAccount=$user->getAccount();
        if($userAccount){
            $quitValues['account_bank'] = $userAccount->bank;
            $quitValues['account_branch'] = $userAccount->branch;
            $quitValues['account_owner'] = $userAccount->owner;
            $quitValues['account_number'] = $userAccount->number;
            $quitValues['account_code'] = $userAccount->code;

            return $quitValues;
        }

        $quit=$this->getByUser($user)->orderBy('date','desc')->first();

        if($quit){
          
            $quitValues['account_bank'] = $quit['account_bank'];
            $quitValues['account_branch'] = $quit['account_branch'];
            $quitValues['account_owner'] = $quit['account_owner'];
            $quitValues['account_number'] = $quit['account_number'];
            $quitValues['account_code'] = $quit['account_code'];
        } 

        return $quitValues;
    }

    public function validateQuitInputs(array $values,Payway $payway)
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

    public function createQuit(Signup $signup,Quit $quit, array $quitDetails)
    {
       
        $quit = DB::transaction(function() use($signup,$quit,$quitDetails) {
            
            $signup->quits()->save($quit);
           
            $quit->details()->saveMany($quitDetails);
           
            $quit=Quit::find($quit->id);

           

            $quit->updateMoney();

            $signup=Signup::find($quit->signupId);
            $signup->updateStatus();
            
            return $quit;
        });
       
        
        $this->setStudentsQuit($quit);

        return $quit;
    }

    function countFee($paywayId, $amount)
    {
       
        return  Payway::find($paywayId)->getFee($amount);
    }

    public function updateQuit(Quit $quit, array $details=[])
    {
       
        $quit = DB::transaction(function() use($quit,$details) {
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

            return $quit;
        });

        
        
    }

    public function addQuitDetails(Quit $quit, array $newDetails=[])
    {
       
        $quit = DB::transaction(function() use($quit,$newDetails) {
            $quit->save();
            $quit->details()->saveMany($newDetails);

            $quit=Quit::find($quit->id);
            $quit->updateMoney();

            $signup=Signup::find($quit->signupId);
            $signup->updateStatus();

            return $quit;
        });

       
        $this->setStudentsQuit($quit);
        
    }

    public function setUnHandled($updatedBy)
    {
        $quits = $this->getByStatus(0)->get();

        foreach($quits as $quit){
            $quit->status = -1;
            $quit->updatedBy=$updatedBy;
            $quit->save();
        } 
       
    }

    public function reviewOK($updatedBy)
    {
        
        $status=1;
        $errors=[];

        $reviewOkQuitsCount=$this->getByStatus($status)->count();
        if($reviewOkQuitsCount){
            $errors['status'] = ['執行審核通過失敗.因為目前還有已審核的退費未結案.'];   
            return $errors;
        } 
        
        
        $quits = $this->getByStatus(0)->get();
        foreach($quits as $quit){
            $quit->status = 1;
            $quit->updatedBy=$updatedBy;
            $quit->save();
        } 

        return [];
    }

    public function finishOK($updatedBy)
    {
        
        $errors=[];
        
        $quits = $this->getByStatus(1)->get();
        foreach($quits as $quit){
            $quit->status = 2;
            $quit->updatedBy=$updatedBy;
            $quit->save();
        } 

        return $errors;
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