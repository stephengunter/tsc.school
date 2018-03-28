<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\User;
use App\Role;
use App\Profile;
use App\Course;
use App\Signup;
use App\SignupDetail;
use App\Services\Users;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Signups 
{
    public function __construct()
    {
        $this->statuses=array(
            ['value'=> 0 , 'text' => '待繳費'],
            ['value'=> 1 , 'text' => '已繳費'],
            ['value'=> -1 , 'text' => '已取消']
        );
        $this->shopId=config('app.bill.shopId');
        $this->with=['bill','details.course.center','user.profile'];

    }
   
    public function getAll()
    {
        return Signup::with($this->with); 
    }
    public function getById($id)
    {   
        return Signup::with($this->with)->find($id);
    }

    public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id', $ids);
    }
        
    public function createSignup(Signup $signup, array $details)
    {
        if($signup->tuitions)   abort(500, '報名表課程費用錯誤');
        $bill=$this->initBill($signup);

      
        $signup=DB::transaction(function() use($signup,$details,$bill) {
            $signup->save();
            $signup->details()->saveMany($details);
            $signup->bill()->save($bill);

            return $signup;
        });

        return $signup;
        
    }

    function initBill(Signup $signup)
    {
        $date = Carbon::today();
        $amount=$signup->amount();
        $code=$this->initBillCode($date, $amount);

        return new Bill([
            'code' => $code,
            'amount' => $amount,
            'deadLine' => $date->addDays(10)
        ]);
        
    }

    function initBillCode($date, $amount)
    {
        $value = rand(10,100*100*100*100);
        return $this->shopId . $value;
    }

    public function deleteSignup(Signup $signup,$updatedBy)
    {
        $signup->removed=true;
        $signup->updatedBy=$updatedBy;
        $signup->save();
        
    }
    
    public function fetchSignups(Term $term,Center $center, Course $course = null)
    {
        $signups=null;
        if($course){
            $signups=$this->fetchSignupsByCourse($course);
        }else{
           
            $signups=$this->fetchSignupsByTermCenter($term, $center);
        }
            
        return $signups;

    }

    public function fetchSignupsByTermCenter(Term $term, Center $center)
    {
        $courseIds=Course::where('removed',false)->where('termId',$term->id)
                                            ->where('centerId',$center->id)
                                            ->pluck('id')->toArray();

        $signupDetails=SignupDetail::whereIn('courseId',$courseIds);

        $signupIds=array_unique($signupDetails->pluck('signupId')->toArray());

        return $this->getByIds($signupIds);
    }

    public function fetchSignupsByCourse(Course $course)
    {
        $signupDetails=SignupDetail::where('courseId',$course->id);

        $signupIds=array_unique($signupDetails->pluck('signupId')->toArray());

        return $this->getByIds($signupIds);
    }

    public function statusOptions()
    {
        return $this->statuses;
    }
    
    
}