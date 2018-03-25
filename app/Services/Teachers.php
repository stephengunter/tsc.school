<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\Teacher;
use App\Wage;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use DB;
use Excel;

class Teachers 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['user.profile','user.contactInfoes.address.district.city'];
    }
    public function getAll()
    {
        return Teacher::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {   
        return Teacher::with($this->with)->find($id);
    }

    public function createTeacher(User $user,Teacher $teacher,Array $wageValues)
    {
        $user->teacher()->save($teacher);
        
        $user->addRole(Role::teacherRoleName());

        $wage=$user->wages->first();
        if($wage) $wage->update($wageValues);
        else $user->wages()->save(new Wage($wageValues));

        return $teacher;
        
    }

    


    public function deleteTeacher(Teacher $teacher,$updatedBy)
    {
        $teacher->removeRole();

        $teacher->removed=true;
        $teacher->updatedBy=$updatedBy;
        $teacher->save();
        
    }
    
    public function fetchTeachers(Center $center = null, bool $reviewed=true,  $keyword = '')
    {
        $teachers=null;
        if($keyword) $teachers=$this->getByKeyword($keyword);
        else $teachers=$this->getAll();

        if($center){
            $teachers=$teachers->whereHas('centers', function($q) use ($center)
            {
                $q->where('id',$center->id );
            });
          
        }

        return $teachers->where('reviewed',$reviewed);
    }

    public function  getByKeyword($keyword)
    {
        $byPhones=User::where('phone', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $bySIDs=Profile::where('sid', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
       
        $userIds=array_unique(array_merge($byPhones,$byFullnames,$bySIDs));

        return $this->getAll()->whereIn('userId' , $userIds );
       
    }
        

    public function getTeacherBySID($sid)
    {
        $profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return Teacher::find($profile->userId);
    }
    
    public function importTeachers($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(20);
            $reader->limitRows(100);
        })->get();

        $teacherList=$excel->toArray()[0];
       
        for($i = 1; $i < count($teacherList); ++$i) {
            $row=$teacherList[$i];

            $center_codes=trim($row['centers']);
            $sid=trim($row['id']);
            $fullname=trim($row['fullname']);

            $gender=(int)trim($row['gender']);
            if($gender) $gender=true;
            else $gender=false;

            $dob=trim($row['dob']);
            if($dob){
                $pieces=explode('/', $dob);
                $year = (int)$pieces[0] + 1911;
                $dob= $year . '/'.$pieces[1]. '/'.$pieces[2];                
            }


            $phone=trim($row['phone']);
            $email=trim($row['email']);
            $zipcode=trim($row['zipcode']);
            $street=trim($row['street']);

            $education=trim($row['education']);
            $specialty=trim($row['specialty']);
            $job=trim($row['job']);
            $description=trim($row['description']);

            $experiences='';               
            $array_experiences = explode(',', trim($row['experiences']));
            for($j = 0; $j < count($array_experiences); ++$j){
                $experiences .= $array_experiences[$j] . '<br>';
            }

            if(!$fullname){
                continue;
            }

            $wage=trim($row['wage']);
            if(!$wage){
                $err_msg .= '鐘點費不可空白' . ',';
                continue;
            }else{
                $wage=floatval($wage);
                if(!$wage){
                    $err_msg .= '鐘點費錯誤' . ',';
                    continue;
                } 
            }

            $account=trim($row['account']);
            if(!$account){
                $err_msg .= '帳號不可空白' . ',';
                continue;
            }

            
            $center_codes=trim($row['centers']);
            if(!$center_codes){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }

            $centerIds=[];
            $center_codes=explode(',', $center_codes);
            foreach($center_codes as $code){
                $center=Center::where('code',$code)->first();
                if(!$center){
                    $err_msg .= '中心代碼' . $code . '錯誤';
                    continue;     
                } 
                array_push($centerIds, $center->id);
            }

            $existTeacher = $this->getTeacherBySID($sid);
            if($existTeacher)
            {
                $existTeacher->centers()->sync($centerIds);
                continue;
            }

            $userValues=[
                'email' => $email,
                'phone' => $phone,
                'updatedBy' => $updatedBy
            ];
            $profileValues=[
                'fullname' => $fullname,
                'sid' => $sid,
                'gender' => $gender,
                'dob' => $dob,
               
                'updatedBy' => $updatedBy
              
            ];   

            $teacherValues=[
                'education' => $education,
                'specialty' => $specialty,
                'description' => $description,
                'experiences' => $experiences,
                'job' => $job,
                'updatedBy' => $updatedBy,
                'removed' => false
            ];

            $wageValues=[
                'money' => $wage,
                'account' => $account,
                'updatedBy' => $updatedBy,
            ];

            $user= $this->users->findUser($email, $phone);

            if(!$user)
            {
                $user=$this->users->createUser(
                    new User($userValues),
                    new Profile($profileValues)
                );
               
            }
         
            $district=null;
            $zipcode=trim($row['zipcode']);
            if($zipcode) $district=District::with(['city'])->where('zipcode',$zipcode)->first();
            if(!$district){
                $err_msg .= '郵遞區號' . $zipcode . '錯誤';
                continue;
            }
          
            $street=trim($row['street']);
           
            $address=new Address([
                'districtId'=>$district->id,
                'street' => $street,
                'updatedBy' => $updatedBy
            ]);
            
            $contactInfo=new ContactInfo([
                'tel'=>'',
                'fax' => '',
            ]);

            $this->users->setContactInfo($user,$contactInfo,$address);
    
            $teacher = new Teacher($teacherValues);

            $teacher=$this->createTeacher($user,$teacher,$wageValues);
     
            $teacher->userId=$user->id;
            $teacher->centers()->sync($centerIds);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}