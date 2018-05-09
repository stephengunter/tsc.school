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
use App\Weekday;
use App\Services\Users;
use App\Services\Import;
use DB;
use Excel;

class Volunteers 
{
    use Import;
    
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
    
    public function getVolunteerBySID($sid)
    {
        $profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return Volunteer::find($profile->userId);
    }

    public function createVolunteer(User $user,Volunteer $volunteer,array $centerIds=[],array $weekdayIds=[])
    {
        $user->volunteer()->save($volunteer);

        $volunteer->userId=$user->id;

        if($centerIds) $volunteer->centers()->sync($centerIds);

        if($weekdayIds) $volunteer->weekdays()->sync($weekdayIds);

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
    
    public function fetchVolunteers(Center $center = null, Weekday $weekday=null, $keyword = '')
    {
        $volunteers=null;
        if($keyword) $volunteers=$this->getByKeyword($keyword);
        else $volunteers=$this->getAll();
        
        if($center){
            $volunteers=$volunteers->whereHas('centers', function($q) use ($center)
            {
                $q->where('id',$center->id );
            });
          
        }

        if($weekday){
            $volunteers=$volunteers->whereHas('weekdays', function($q) use ($weekday)
            {
                $q->where('id',$weekday->id );
            });
          
        }
        

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

            $fullname=trim($row['fullname']);
            if(!$fullname) continue;

            $center_codes=trim($row['centers']);
            if(!$center_codes){
                $err_msg .= '中心代碼不可空白' . ',';
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
            $existVolunteer = $this->getVolunteerBySID($sid);
            if($existVolunteer)
            {
                foreach($centers as $center){
                    $existVolunteer->addToCenter($center);
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
            

            $weekdayValues=trim($row['weekdays']);
            $time=trim($row['time']);
            $ps=trim($row['ps']);

            $weekdayIds=[];
            if($weekdayValues){
                $weekdayValues=explode(',', $weekdayValues);
                $weekdayIds=Weekday::where('val',$weekdayValues)->pluck('id')->toArray();
            }
            
            $volunteer = new Volunteer([
                'time' => $time,
                'ps' => $ps,
                'updatedBy' => $updatedBy
            ]);
            

            $volunteer=$this->createVolunteer($user,$volunteer,$centerIds,$weekdayIds);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}