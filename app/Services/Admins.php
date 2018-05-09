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
use App\Services\Import;
use DB;
use Excel;

class Admins 
{
    use Import;

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

    public function createAdmin(User $user,Admin $admin,string $roleName,array $centerIds=[])
    {
        $user->admin()->save($admin);
        
        $user->addRole($roleName);

        $admin->userId=$user->id;
        if($centerIds) $admin->centers()->sync($centerIds);

        return $admin;
        
    }
   


    public function deleteAdmin(Admin $admin)
    {
        $admin->user->removeRole(Role::staffRoleName());
        $admin->user->removeRole(Role::bossRoleName());
        $admin->delete();
        
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

            $fullname=trim($row['fullname']);
            if(!$fullname) continue;

            $center_codes=trim($row['centers']);
            if(!$center_codes){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }

            $role=trim($row['role']);
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

            //取得中心
            $getCenters=$this->getCenters($row);
            if(array_key_exists('err',$getCenters)){
                $err_msg .= $getCenters['err'] . ',';
                continue;
            }
            $centers=$getCenters['centers'];
            $centerIds=array_map(function($item){
                return $item->id;
            }, $centers);

           
          
            //取得User資料
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
            $existAdmin = $this->getAdminBySID($sid);
            if($existAdmin)
            {
                foreach($centers as $center){
                    $existAdmin->addToCenter($center);
                }
               
                continue;
            }
            
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
    
            $admin = new Admin([
                'active' => true,
                'updatedBy' => $updatedBy
            ]);

            $admin=$this->createAdmin($user,$admin,$roleName,$centerIds);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}