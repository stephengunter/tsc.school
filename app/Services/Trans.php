<?php

namespace App\Services;
use App\User;
use App\Student;
use App\Profile;
use App\Center;
use App\Tran;
use App\Course;
use App\Signup;
use App\SignupDetail;
use App\Services\Users;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Signups;
use DB;
use Carbon\Carbon;
use App\Events\StudentTrans;

class Trans 
{
    public function __construct(Users $users,Centers $centers,Courses $courses,Signups $signups)
    {
        $this->users=$users;
        $this->signups=$signups;
        $this->centers=$centers;
        $this->courses=$courses;
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

    public function fetchTrans($key,$termId)
    {
        $selectedCenterIds=$this->centers->getCentersByKey($key)
                                         ->pluck('id')->toArray();
        $courseIds=$this->courses->getByTerm($termId)
                                ->whereIn('centerId', $selectedCenterIds)
                                ->pluck('id')->toArray();


        $tranIds = Student::whereIn('courseId', $courseIds)
                            ->pluck('tran_id_from')->toArray(); 
                            
        $tranIds = array_filter($tranIds);
      
        $trans=$this->getAll()->whereIn('id',$tranIds);
       
       
        return $trans;
    }

    public function createTran(Student $student,array $tranValues,Course $newCourse)
    {
       
        $tran= DB::transaction(function() use($student,$tranValues,$newCourse) {
            
            $signupDetail=$this->signups->getSignupDetailsByUser($student->user)
                                    ->where('courseId',$student->courseId)
                                    ->first();
            
            $signup=$signupDetail->signup;
            $newDetail = new SignupDetail([
                'courseId' => $newCourse->id,
                'tuition' => $newCourse->tuition,
                'cost' => $newCourse->cost,
                'updatedBy' => $tranValues['updatedBy']
            ]);
            $signup->details()->save($newDetail);


            
            $tranValues['signupDetailId']=$signupDetail->id;
            $tran=Tran::create($tranValues);
            
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

            $signup->updateStatus();

           

           
            return $tran;
        });
        
        
    

        return $tran;
    }

    

    public function xxcreateTran(Tran $tran,Student $student)
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