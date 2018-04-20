<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\User;
use App\Role;
use App\Profile;
use App\Course;
use App\Signup;
use App\Bill;
use App\SignupDetail;
use App\Services\Bills;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Signups 
{
    public function __construct(Bills $bills)
    {
        $this->statuses=array(
            ['value'=> 0 , 'text' => '待繳費'],
            ['value'=> 1 , 'text' => '已繳費'],
            ['value'=> -1 , 'text' => '已取消']
        );
        $this->shopId=config('app.bill.shopId');
        $this->with=['bill.payway', 'quit.details' ,'details.course.center','user.profile'];

        $this->bills=$bills;

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
        if(!$signup->tuitions)   abort(500, '報名表課程費用錯誤');
        $signup->status=0;
        $bill=$this->bills->initBill($signup);
      
        $signup=DB::transaction(function() use($signup,$details,$bill) {
            $signup->save();
            $signup->details()->saveMany($details);
            $signup->bill()->save($bill);

            return $signup;
        });

        return $signup;
        
    }

    public function updateSignup(Signup $signup, array $details)
    {
        if(!$signup->tuitions)   abort(500, '報名表課程費用錯誤');
        
        DB::transaction(function() use($signup,$details) {
            
            $ids= array_map(function($item){
                if($item['id']) return $item['id'];
                return 0;
            },$details);

            SignupDetail::where('signupId',$signup->id)->whereNotIn('id',$ids)->delete();

            
            
            $newDetails=array_filter($details,function($item){
                return !$item['id'];
            });

            $signup->save();
            $signup->details()->saveMany($newDetails);

            $signup->bill->update([
                'amount' => $signup->amount(),
                'deadLine' => Carbon::today()->addDays(10)
            ]);

            
        });
        
    }
    

    public function deleteSignup(Signup $signup,$updatedBy)
    {
        if($signup->status>0) abort(500);
        $signup->delete();
        
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

    public function fetchSignupsByUser(User $user)
    {
        return $this->getAll()->where('userId',$user->id);
    }

    public function getSignupSummary(Term $term,Center $center, Course $course = null)
    {
        $signups=$this->fetchSignups($term,$center,$course)->get();
        return [
            'total' => $signups->count(),
            'no' => $signups->where('status', 0 )->count(),
            'ok' => $signups->where('status', 1 )->count(),
            'canceled' =>  $signups->where('status', -1 )->count()
        ];

    } 

    public function getSignupDetailsByUser(User $user)
    {
        //有效的(未取消的)報名紀錄
        $recordIds = Signup::where('userId', $user->id)
                            ->where('status', '>=', 0)->pluck('id')->toArray();
			
       
		return SignupDetail::whereIn('signupId',$recordIds);
    }

    

    public function statusOptions()
    {
        return $this->statuses;
    }
    
    
}