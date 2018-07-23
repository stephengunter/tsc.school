<?php

namespace App\Services;
use App\Term;
use App\Center;
use App\User;
use App\Role;
use App\Profile;
use App\Course;
use App\Signup;
use App\Payway;
use App\Bill;
use App\SignupDetail;
use App\Discount;
use App\Services\Users;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Bills;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Discounts;
use Carbon\Carbon;
use App\Core\Helper;
use App\Services\Import;
use DB;
use Excel;

class Signups 
{
    use Import;

    public function __construct(Users $users, Bills $bills,Centers $centers,Discounts $discounts,Courses $courses)
    {
        $this->statuses=array(
            ['value'=> 0 , 'text' => '待繳費'],
            ['value'=> 1 , 'text' => '已繳費'],
            ['value'=> -1 , 'text' => '已取消']
        );
        $this->shopId=config('app.bill.shopId');
        $this->with=['bills.payway', 'quits.details' ,'details.course.center','user.profile'];

        $this->bills=$bills;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->discounts=$discounts;

        $this->users=$users;

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

    public function checkSelectedCourses($selectedCourses)
    {
       
        $errors=[];
        $termIds=array_unique($selectedCourses->pluck('termId')->all());
        if(count($termIds) > 1)
        {
            $errors['courseIds'] = ['報名課程必須在同一學期'];
            return $errors;
        }  
       
        //課程必須在同一中心分類
        $centerIds= $selectedCourses->pluck('centerId')->all();
        $centerKeys=$this->centers->getByIds($centerIds)->pluck('key')->toArray();
        if(count(array_unique($centerKeys)) > 1) $errors['courseIds'] = ['報名課程必須在同一開課中心分類'];

        return $errors;
    }

    public function initSignupDetails(User $user,$selectedCourses, $updatedBy)
    {
       
        $errors=[];
        $signupDetails=[];

          //User報名過的課程記錄
        $coursesSignupedIds = [];
        $userSignupDetailRecords = $this->getSignupDetailsByUser($user);
        $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
        foreach ($selectedCourses as $selectedCourse)
        {
          
            if (in_array($selectedCourse->id, $coursesSignupedIds)){
                $errors['courseIds'] = ['此學員已經報名過課程' . $selectedCourse->fullName() ];
                break;
            }else{
                $detail = new SignupDetail([
                    'courseId' => $selectedCourse->id,
                    'tuition' => $selectedCourse->tuition,
                    'cost' => 0,
                    'updatedBy' => $updatedBy
    
                ]);
                array_push($signupDetails,$detail);
            }

            
        }

        return [
            'signupDetails' => $signupDetails,
            'errors' => $errors
        ];
        
       
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
        $date=null;
        if($signup->date) $date=Carbon::parse($signup->date);
        else $date=Carbon::today();
        
        $course=$this->courses->getById($details[0]['courseId']);
       
        $identityIds=explode(',', $signup['identity_ids']);

        
        $bestDiscount=$this->discounts->findBestDiscount($course->center,$course->term,$user,$identityIds,$lotus, count($details),$date);
      
        $this->setSignupDiscount($signup,$course->term,$bestDiscount);
       
        $signup=DB::transaction(function() use($signup,$details) {
            $signup->save();
            $signup->details()->saveMany($details);

            $signup->updateStatus();

            return $signup;
        });

        return $signup;
        
    }

    public function updateSignup(Signup $signup, array $newDetails=[],bool $lotus=false)
    {
        $date=null;
        if($signup->date) $date=Carbon::parse($signup->date);
        else $date=Carbon::today();

        $course=$this->courses->getById($signup->details[0]['courseId']);

        $identityIds=explode(',', $signup['identity_ids']);

        $courseCount=$signup->details()->where('canceled',false);
        
        $courseCount= $signup->getValidCoursesCount() + count($newDetails);
        
        $bestDiscount=$this->discounts->findBestDiscount($course->center,$course->term,$signup->user,$identityIds,$lotus, $courseCount,$date);
      
        $this->setSignupDiscount($signup,$course->term,$bestDiscount);
       
        DB::transaction(function() use($signup,$newDetails) {
            $signup->save();
            $signup->details()->saveMany($newDetails);

            $signup=Signup::find($signup->id);
       
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

    public function importPays($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(20);
            $reader->limitRows(100);
        })->get();

        $signupList=$excel->toArray()[0];
       
        for($i = 1; $i < count($signupList); ++$i) {
            $row=$signupList[$i];

            $fullname=trim($row['fullname']);
            if(!$fullname) continue;

            $courses=[];               
            $array_courses = explode(',', trim($row['courses']));
            for($j = 0; $j < count($array_courses); ++$j){
                $course_number=$array_courses[$j];
                $course =Course::where('number',$course_number)->first();
                
                if(!$course){
                    $err_msg .= $course_number . '課程不存在' . ',';
                    continue;
                }else{
                    array_push($courses, $course);
                }
            }

           
            $date=trim($row['date']);
            try {  
                    $date=Carbon::parse($date);
    
                }catch (Exception $e) {  
                    $err_msg .= $fullname . '報名日期錯誤' . ',';
                }  

            $pay=false;
            $payway=null;
            $pay_date=trim($row['pay_date']);
            $pay_amount=trim($row['pay_amount']);
            $paywayCode=trim($row['pay_way']);
            if($pay_date){
                try {  
                    $pay_date=Carbon::parse($pay_date);
    
                }catch (Exception $e) {  
                    $err_msg .= $fullname . '繳費日期錯誤' . ',';
                }  

                $pay_amount=floatval($pay_amount);
                if(!$pay_amount){
                    $err_msg .= $fullname . '繳費金額錯誤' . ',';
                    continue;
                }

                $payway=Payway::where('code',$paywayCode)->first();
                if(!$payway){
                    $err_msg .= $fullname . '繳費方式錯誤' . ',';
                    continue;
                }

                $pay=true;

            }
          

            $userDatas=$this->getImportUserDatas($row,$updatedBy);
            if(array_key_exists('err',$userDatas)){
                $err_msg .= $userDatas['err'] . ',';
                continue;
            }

            $userValues=$userDatas['userValues'];
            $profileValues=$userDatas['profileValues'];
           
            $contactInfoValues=$userDatas['contactInfoValues'];
            $addressValues=$userDatas['addressValues'];
            $identities=$userDatas['identities'];

            $sid=$profileValues['sid'];
            $user= $this->users->findBySID($sid);

            if(!$user)
            {
                $user=$this->users->createUser(
                    new User($userValues),
                    new Profile($profileValues)
                );
               
            }

            foreach($identities as $identity){
                $user->addIdentity($identity->id);
            }

            $contactInfo=new ContactInfo($contactInfoValues);
            $address=new Address($addressValues);
            

            $this->users->setContactInfo($user,$contactInfo,$address);

            $result=$this->initSignupDetails($user,$courses,$updatedBy);
       
            $errors=$result['errors'];
            if($errors){
                $err_msg .= $fullname . '報名課程錯誤' . ',';
            }  

           

            $signupDetails=$result['signupDetails'];

            $lotus=trim($row['lotus']);
            $lotus=Helper::isTrue($lotus);
            

            $signup=new Signup(Signup::init());
            $signup->userId=$user->id;

            $identityIds = array_map(function($item){
                return $item->id;
            }, $identities);


            $signup->identity_ids=join(',', $identityIds);
            $signup->net=false;
            $signup->updatedBy=$updatedBy;
            
            $signup=$this->createSignup($signup,$signupDetails,$user,$lotus);


           

            if($pay){
                $bill=Bill::where('signupId', $signup->id)->first();
                if($pay_amount!=floatval($bill->amount)){
                    $err_msg .= $fullname . '繳費金額錯誤' . ',';
                    continue;
                }
                $bill->updatedBy=$updatedBy;
                $this->bills->payBill($bill,$payway,$pay_date);
            }
        }
    }

    public function importSignups($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(20);
            $reader->limitRows(300);
        })->get();

        $signupList=$excel->toArray()[0];
       
        for($i = 1; $i < count($signupList); ++$i) {
            $row=$signupList[$i];

            $fullname=trim($row['fullname']);
            if(!$fullname) continue;

            $courses=[];               
            $array_courses = explode(',', trim($row['courses']));
            for($j = 0; $j < count($array_courses); ++$j){
                $course_number=$array_courses[$j];
                $course =Course::where('number',$course_number)->first();
                
                if(!$course){
                    $err_msg .= $course_number . '課程不存在' . ',';
                   
                    continue;
                }else{
                    array_push($courses, $course);
                }
            }
         
           
            $date=trim($row['date']);
            
            if($date){
                try {  
                    $date=Carbon::parse($date);
    
                }catch (Exception $e) {  
                    $err_msg .= $fullname . '報名日期錯誤' . ',';
                }     
            }

            $userDatas=$this->getImportUserDatas($row,$updatedBy);
            if(array_key_exists('err',$userDatas)){
                dd($userDatas['err'] );
                $err_msg .= $userDatas['err'] . ',';
                continue;
            }


            $userValues=$userDatas['userValues'];
            $profileValues=$userDatas['profileValues'];
           
            $contactInfoValues=$userDatas['contactInfoValues'];
            $addressValues=$userDatas['addressValues'];
            $identities=$userDatas['identities'];

            $sid=$profileValues['sid'];
            $user= $this->users->findBySID($sid);

            if($user)
            {
				$this->users->updateUser($user,$userValues,$profileValues);
               
            }else{
				$user=$this->users->createUser(
                    new User($userValues),
                    new Profile($profileValues)
                );
			}
          
            foreach($identities as $identity){
                $user->addIdentity($identity->id);
            }

            $contactInfo=new ContactInfo($contactInfoValues);
            $address=new Address($addressValues);
            $this->users->setContactInfo($user,$contactInfo,$address);

            continue;

            $result=$this->initSignupDetails($user,$courses,$updatedBy);
       
            $errors=$result['errors'];
            if($errors){
                $err_msg .= $fullname . '報名課程錯誤' . ',';
            }  

           

            $signupDetails=$result['signupDetails'];

            $lotus=trim($row['lotus']);
            $lotus=Helper::isTrue($lotus);
            

            $signup=new Signup(Signup::init());
            $signup->userId=$user->id;

            $identityIds = array_map(function($item){
                return $item->id;
            }, $identities);


            $signup->identity_ids=join(',', $identityIds);
            $signup->net=false;
            $signup->updatedBy=$updatedBy;
            
            $signup=$this->createSignup($signup,$signupDetails,$user,$lotus);
    
            
            
           
        }  //end for  

        
        

     

        return $err_msg;

       



   }
    
    
}