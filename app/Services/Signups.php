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
use App\Discount;
use App\Services\Bills;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;
use DB;
use Carbon\Carbon;
use App\Core\Helper;

class Signups 
{
    public function __construct(Bills $bills,Centers $centers,Discounts $discounts,Courses $courses)
    {
        $this->statuses=array(
            ['value'=> 0 , 'text' => '待繳費'],
            ['value'=> 1 , 'text' => '已繳費'],
            ['value'=> -1 , 'text' => '已取消']
        );
        $this->shopId=config('app.bill.shopId');
        $this->with=['bill.pays.payway', 'quits.details' ,'details.course.center','user.profile'];

        $this->bills=$bills;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->discounts=$discounts;

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

    public function getUserCanAddDetailSignup(Term $term, Center $center,User $user)
    {
        $centerIds=$this->centers->getCentersByKey($center->key)->pluck('id')->toArray();
        
        $courseIds=$this->courses->fetchCourses($term->id)
                                 ->whereIn('centerId',$centerIds)
                                 ->pluck('id')->toArray();
                                
        $signupDetails=SignupDetail::whereIn('courseId',$courseIds);

        $signupIds=array_unique($signupDetails->pluck('signupId')->toArray());


        $userSignups=$this->getByIds($signupIds)->where('userId',$user->id)->get();
       
        
        $userSignups = $userSignups->filter(function ($signup) {
            return $signup->canAddDetail();
        });
        
        return  $userSignups->first();
    }

    function setSignupDiscount(Signup $signup,Term $term, Discount $discount)
    {
        $signup->discountId=$discount->id;

        if($term->canBird(Carbon::today())){
        
           
            $signup->points = $discount->pointOne;

            if ($signup->hasDiscount())
            {
                $signup->discount = $discount->name;
            }

            if ($discount->bird())
            {
                $signup->discount .= " - 早鳥優惠";
            }

        }else{
        
            if ($signup->hasDiscount())
            {
                $signup->discount = $discount->name;
            }

            $signup->points = $discount->pointTwo;
        }
    }
        
    public function createSignup(Signup $signup, array $details,User $user, bool $lotus=false)
    {
        $course=$this->courses->getById($details[0]['courseId']);
       
        $identityIds=explode(',', $signup['identity_ids']);

        
        $bestDiscount=$this->discounts->findBestDiscount($course->center,$course->term,$user,$identityIds,$lotus, count($details));
      
        $this->setSignupDiscount($signup,$course->term,$bestDiscount);
       
        $signup=DB::transaction(function() use($signup,$details) {
            $signup->save();
            $signup->details()->saveMany($details);
            $signup->bill()->save(new Bill([]));

            $signup->updateMoney();

            return $signup;
        });

        return $signup;
        
    }

    public function updateSignup(Signup $signup, array $newDetails=[],bool $lotus=false)
    {
       
        $course=$this->courses->getById($signup->details[0]['courseId']);

        $identityIds=explode(',', $signup['identity_ids']);

        $courseCount=$signup->details()->where('canceled',false);
        
        $courseCount= $signup->getValidCoursesCount() + count($newDetails);
        
        $bestDiscount=$this->discounts->findBestDiscount($course->center,$course->term,$signup->user,$identityIds,$lotus, $courseCount);
      
        $this->setSignupDiscount($signup,$course->term,$bestDiscount);
       
        DB::transaction(function() use($signup,$newDetails) {
            $signup->save();
            $signup->details()->saveMany($newDetails);

            $signup=Signup::find($signup->id);
            $signup->updateMoney();
            $signup->updateStatus();
        });
        
    }

   

    public function deleteSignup(Signup $signup,$updatedBy)
    {
        if(!$signup->canDelete()) abort(500);
        $signup->delete();
        
    }
    
    public function fetchSignups(Term $term,Center $center, Course $course = null)
    {
        if($course) return $this->fetchSignupsByCourse($course);
        else  return $this->fetchSignupsByTermCenter($term, $center);

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