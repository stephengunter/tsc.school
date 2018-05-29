<?php

namespace App\Services;

use App\User;
use App\Profile;
use App\Role;
use App\ContactInfo;
use App\Address;
use App\District;
use Carbon\Carbon;
use App\Core\Helper;
use DB;

class Users
{
	public function __construct()
    {
        $this->with=['profile','contactInfoes.address.district.city'];
	}
	public function getAll()
    {
        return User::with($this->with); 
	}
	public function getById($id)
    {   
        return $this->getAll()->find($id);
	}
	public function getByIds(array $ids)
    {   
        return $this->getAll()->whereIn('id',$ids);
	}

	public function findByName($name)
	{
		
		return $this->getAll()->where('name',$name)->first();

	}

	public function getByNames(array $usernames)
    {   
        return $this->getAll()->whereIn('name',$usernames);
	}
	
	public function fetchUsers(Role $role = null, $keyword = '')
	{
		$users=null;
		if($keyword) $users=$this->getByKeyword($keyword);
		else $users=$this->getAll();

		if ($role)
		{
			$users=$users->whereHas('roles', function($query) use ($role)
			{
				$query->where('id',$role->id );
			});

		}

		
		return $users;
	}
		
	public function  getByKeyword($keyword)
	{
		$byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
		$byUserNames=User::where('name', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
		
		$userIds=array_unique(array_merge($byFullnames,$byUserNames));

		return $this->getAll()->whereIn('id' , $userIds );
		
	}

	public function getDefaultPassword(User $user,Profile $profile=null)
	{
		if(!$profile) $profile=$user->profile;
		if ($profile->dob){
			$dob=new Carbon($profile->dob);
			return Helper::toTaipeiDateString($dob);
		} 
		return config('app.user.default_pw');
	}
    
    public function createUser(User $user,Profile $profile, array $roleNames=[])
    {
		$sid=strtoupper($profile->sid);
		$profile->sid=$sid;
		$user->name=$sid;

		if (!$user->password) $user->password = $this->getDefaultPassword($user,$profile);

		
		if(Helper::isTaiwanSID($sid)){
			$profile->gender = Helper::getGenderFromSID($sid);
		}
		
		$user= DB::transaction(function() use($user,$profile) {
            $user->save();
            $user->profile()->save($profile);
            return $user;
		});
		
		if($roleNames){
			foreach($roleNames as $roleName){
				$this->addRole($roleName);
			}	
		}

		return $user;
        
	}

	

	public function updateUser(User $user,array $userValues,array $profileValues)
	{
		$sid=strtoupper($profileValues['sid']);
        
		if(Helper::isTaiwanSID($sid)){
			$profileValues['gender'] = Helper::getGenderFromSID($sid);
		}

		DB::transaction(function() use($user,$userValues,$profileValues) {
			
			$user->profile->update($profileValues);
			$user->update($userValues);
		});

	}


	
	

	public function findUsers($email, $phone, $sid)
	{
		$byEmails=[];
		if($email){
			$email=strtolower($email);
			$byEmails=User::where('email', $email)->pluck('id')->toArray();
		} 

		$byPhones=[];
		if($phone){
		
			$byPhones=User::where('phone', $phone)->pluck('id')->toArray();
		} 

		$bySIDs=[];
		if($sid){
			$sid=strtoupper($sid);
			$bySIDs=Profile::where('sid', $sid)->pluck('userId')->toArray();
		} 
       
		$userIds=array_unique(array_merge($byPhones,$byEmails,$bySIDs));

        return User::with(['profile'])->whereIn('id' , $userIds );
	}

	public function findBySID($sid)
	{
		
		return $this->findByName(strtoupper($sid));
		// $profile=Profile::where('sid',$sid)->first();
        // if(!$profile) return null;

        // return User::find($profile->userId);
	}

	public function getBySIDs(array $sids)
	{
		$sids=array_map(function($sid){
			return strtoupper($sid);
		}, $sids);

		return $this->getByNames($sids);
		
		// $userIds=Profile::whereIn('sid',$sids)->pluck('userId')->toArray();
		
		// return $this->getByIds($userIds);
       
	}
	
	public function addRole(User $user ,string $roleName)
	{
		$role=Role::getByName($roleName);
		
		$user->roles()->attach($role);
	}
	
	public function setContactInfo(User $user, ContactInfo $contactInfo , Address $address)
	{
		$exist=$user->getContactInfo();
		if($exist){
			$exist->address->update($address->toArray());
			$exist->update($contactInfo->toArray());
		}else{
			DB::transaction(function() use($user,$contactInfo,$address) {
				$user->contactInfoes()->save($contactInfo);
				$contactInfo->address()->save($address);
			});
		}
	}

	public function addIdentitiesToUser(User $user,array $identityIds)
	{
		foreach($identityIds as $identityId){
			$user->addIdentity($identityId);
		}
	}

	public function getOrdered($users)
    {
        return $users->orderBy('id','desc');
	}
	
	public function validateUserInputs(array $values, string $roleName='')
	{
		
		$needSID=true;
		$needDOB=true;
		$needFullname=true;

		return $this->validateInputs($values, $needFullname,$needSID,$needDOB);


		if(!$roleName){
			$needFullname=false;
			return $this->validateInputs($values, $needFullname,$needSID,$needDOB);
		}

		

		$role=Role::where('name',$roleName)->first();
		if($role->name==Role::studentRoleName()){
			$needFullname=true;
			$needSID=true;
			$needDOB=true;
			return $this->validateInputs($values, $needFullname,$needSID,$needDOB);
		}
		else if($role->name==Role::volunteerRoleName()){
			
			$needFullname=true;
			$needSID=false;
			$needDOB=true;
			return $this->validateInputs($values, $needFullname,$needSID,$needDOB);
		}


		$needFullname=true;
		$needSID=true;
		$needDOB=true;
		return $this->validateInputs($values, $needFullname,$needSID,$needDOB);
	}

	public function validateInputs($values, bool $needFullname=true,bool $needSID=false,bool $needDOB=false)
    {
		$errors=[];

        $id=0;        
		if(array_key_exists ('id' ,$values)) $id=(int)$values['id']; 
		
		$email=$values['email'];
		$phone=$values['phone'];
		$existUser=null;
		if(!$phone){
			$errors['user.phone'] = ['必須填寫手機'];
		}

		if($needFullname){
			$fullname=$values['profile']['fullname'];
			if(!$fullname) $errors['user.profile.fullname'] = ['必須填寫姓名'];
		}

		$sid=$values['profile']['sid'];

		if($sid){
			
			if(Helper::isTaiwanSID($sid)){
				$isValid=Helper::checkSID($sid);
				if(!$isValid) $errors['user.profile.sid'] = ['身分證號錯誤'];
			}

			$existUser=$this->findBySID($sid);
			if($existUser && $existUser->id!=$id){
				$errors['user.profile.sid'] = ['身分證號重複了'];
			} 
		}else{
			//沒填身分證號
			if($needSID){
				$errors['user.profile.sid'] = ['必須填寫身分證號'];
			}

			
		}

		if($needDOB){
			$dob=$values['profile']['dob'];
			if(!$dob) $errors['user.profile.dob'] = ['必須填寫生日'];
				
		}
		

        return $errors;
	}
	
	
}