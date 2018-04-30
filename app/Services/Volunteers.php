<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\Volunteer;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use DB;
use Excel;

class Volunteers 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['user.roles','user.profile','user.contactInfoes.address.district.city'];
    }
    public function getAll()
    {
        return Volunteer::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {   
        return Volunteer::with($this->with)->find($id);
    }
    public function getByIds(array $ids)
    {   
        return Volunteer::with($this->with)->whereIn('userId',$ids);
    }
    public function getBySIDs(array $sids)
	{
		$users=$this->users->getBySIDs($sids);
		
		$userIds=$users->pluck('id')->toArray();
		
		return $this->getByIds($userIds);
       
	}

    public function createVolunteer(User $user,Volunteer $volunteer)
    {
        $user->volunteer()->save($volunteer);
        $user->addRole(Role::volunteerRoleName());
        
        return $volunteer;
        
    }

    

    public function deleteVolunteer(Volunteer $volunteer,$updatedBy)
    {
        $volunteer->removeRole();

        $volunteer->removed=true;
        $volunteer->updatedBy=$updatedBy;
        $volunteer->save();
        
    }
    
    public function fetchVolunteers($keyword = '')
    {
        $volunteers=null;
        if($keyword) $volunteers=$this->getByKeyword($keyword);
        else $volunteers=$this->getAll();

        

        return $volunteers;
    }

    public function  getByKeyword($keyword)
    {
        $byPhones=User::where('phone', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $bySIDs=Profile::where('sid', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
       
        $userIds=array_unique(array_merge($byPhones,$byFullnames,$bySIDs));

        return $this->getAll()->whereIn('userId' , $userIds );
       
    }
   
    public function  options()
    {
       
        $volunteers=$this->getAll()->get();
       
        return $volunteers->map(function ($volunteer) {
            return $volunteer->toOption();
        })->all();
    }

    public function getVolunteerBySID($sid)
    {
        $profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return Volunteer::find($profile->userId);
    }

    
    
    public function importVolunteers($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(20);
            $reader->limitRows(100);
        })->get();

        $volunteerList=$excel->toArray()[0];
       
        for($i = 1; $i < count($volunteerList); ++$i) {
            $row=$volunteerList[$i];

           
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

            if(!$fullname){
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

            $volunteerValues=[
                
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


            $existVolunteer = Volunteer::find($user->id);
            if($existVolunteer)
            {
                $existVolunteer->addRole();
               
            }else{
                $volunteer = new Volunteer([
                    'updatedBy' => $updatedBy,
                    'active' => true,
                    'removed' => false
                ]);
    
                $volunteer=$this->createVolunteer($user,$volunteer);
            }

         
            
                
            
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}