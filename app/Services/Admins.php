<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\Admin;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use DB;
use Excel;

class Admins 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['user.profile','user.contactInfoes.address.district.city'];
    }
    public function getAll()
    {
        return Admin::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {
        return Admin::with($this->with)->find($id);
    }

    public function createAdmin(User $user,Admin $admin,$roleName)
    {
        $user->admin()->save($admin);
        
        $user->addRole($roleName);

        return $admin;
        
    }
   
    
    public function fetchAdmins(Center $center = null, $keyword = '')
    {
        $admins=null;
        if($keyword) $admins=$this->getByKeyword($keyword);
        else $admins=$this->getAll();

        if($center){
            $admins=$admins->whereHas('centers', function($q) use ($center)
            {
                $q->where('id',$center->id );
            });
          
        }
  

        return $admins;
    }

    public function  getByKeyword($keyword)
    {
        $byPhones=User::where('phone', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $bySIDs=Profile::where('sid', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
       
        $userIds=array_unique(array_merge($byPhones,$byFullnames,$bySIDs));

        return $this->getAll()->whereIn('userId' , $userIds );
       
    }
        

    public function getAdminBySID($sid)
    {
        $profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return Admin::find($profile->userId);
    }
    
    public function importAdmins($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $adminList=$excel->toArray()[0];
       
        for($i = 1; $i < count($adminList); ++$i) {
            $row=$adminList[$i];

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

            $role=trim($row['role']);

            if(!$fullname){
               continue;
            }

            $center_codes=trim($row['centers']);
            if(!$center_codes){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }

            if(!$role){
                $err_msg .= '角色名稱不可空白' . ',';
                continue;
            }

         
            $roleName='';
            if(strtolower($role) == strtolower(Role::staffRoleName()) ){
                $roleName=Role::staffRoleName();
            }else if(strtolower($role) == strtolower(Role::bossRoleName()) ){
                $roleName=Role::bossRoleName();
            }else{
                $err_msg .= '角色名稱' . $role . '錯誤,';
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

            $existAdmin = $this->getAdminBySID($sid);
            if($existAdmin)
            {
                $existAdmin->centers()->sync($centerIds);
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
    
            $admin = new Admin([
                'active' => true,
                'updatedBy' => $updatedBy
            ]);

            $admin=$this->createAdmin($user,$admin,$roleName);
     
            $admin->userId=$user->id;
            $admin->centers()->sync($centerIds);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}