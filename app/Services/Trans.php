<?php

namespace App\Services;
use App\User;
use App\Student;
use App\Profile;
use App\Center;
use App\Tran;
use App\Course;
use App\Services\Users;
use DB;
use Carbon\Carbon;
use App\Events\StudentTrans;

class Trans 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['signupDetail.signup','course.center'];
    }

    public function getAll()
    {   
        return Tran::with($this->with);
    }
    
    public function getById($id)
    {   
        return Tran::with($this->with)->find($id);
    }

    

    public function createTran(Tran $tran,Student $student)
    {
       
        $tran= DB::transaction(function() use($tran,$student) {
            $tran->save();
            
            $newStudent =Student::create([
                'tran_id_from' => $tran->id,
                'status' => 1,
                'userId' => $student->userId,
                'courseId' => $tran->courseId,
                'joinDate' => Carbon::today()
            ]);

            $student->update([
                'tran_id_to' => $tran->id,
                'status' => -1, 
            ]);
           
            return $tran;
        });
        
        event(new StudentTrans($tran));
    

        return $tran;
    }

    
    
   

    
    
}