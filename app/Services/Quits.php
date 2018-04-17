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
use DB;

class Quits 
{
    public function __construct()
    {
        $this->with=['signup','details','payway'];
    }
    
    public function getById($id)
    {   
        return Quit::with($this->with)->find($id);
    }

    public function createQuit(Signup $signup,Quit $quit, array $quitDetails)
    {
        $quit= DB::transaction(function() use($signup,$quit,$quitDetails) {
            
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
        DB::transaction(function() use($quit,$quitValues,$detailsValues) {
           
            foreach($detailsValues as $detail){
                $quitDetail=QuitDetail::find($detail['id']);

                $percents=(int)$detail['percents'];
               
                if(!$percents){
                    $signupDetail=SignupDetail::find($detail['signupDetailId']);
                    $student= $signupDetail->getStudent();
                    $student->update([
                        'status' => 1, 
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

    public function deleteQuit(Quit $quit , $updatedBy)
    {
        DB::transaction(function() use($quit,$updatedBy) {
           
           

            
           
		});
    

       
    }

    
    
   

    
    
}