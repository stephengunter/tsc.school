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
use DB;

class Quits 
{
    public function __construct()
    {
        
        $this->with=['signup'];
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
            
            $signup->quit()->save($quit);

            $signup->quit->details()->saveMany($quitDetails);

           
            $signup->update([
                'status' => -1
            ]);

            $signupDetailIds=collect($quitDetails)->pluck('signupDetailId')->toArray();
            
            foreach($signup->details as $signupDetail){
                if(in_array($signupDetail->id, $signupDetailIds)){
                    $student= $signupDetail->getStudent();
                    $student->update([
                        'status' => -1, 
                        'ps' => $quit->date . '辦理退費退出此課程' 
                    
                    ]);
                } 
               
            }
            
            return $quit;
		});
    

        return $quit;
    }

    

    
    
   

    
    
}