<?php

namespace App\Services;

use App\User;
use App\Profile;
use App\Role;
use App\ContactInfo;
use App\Address;
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
        return User::with($this->with)->find($id);
	}
	public function getByIds(array $ids)
    {   
        return User::with($this->with)->whereIn('id',$ids);
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
    
    public function createUser(User $user,Profile $profile, array $roleNames=[])
    {
		$userName = $user->email;
		if (!$user->email) $userName = $user->phone;
		$user->name=$userName;

		if(!$user->password) $user->password = '000000';
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

	public function storeUser($email, $phone, Profile $profile, array $roleNames=[])
	{
		$userName = $email;
		if (!$email) $userName = $phone;

		$user=new User([
			'name' => $userName,
			'email' => $email,
			'phone' => $phone
		]);
		

		return $this->createUser($user,$profile, $roleNames);

	}

	public function updateUser(User $user,array $values)
	{
		$userName = $values['email'];
		if (!$userName) $userName = $values['phone'];

		if(!$userName) abort(500); 

		$values['name'] = $userName;

		$user->update($values);
	}


	public function findByName($name)
	{
		
		return User::where('name',$name)->first();

	}

	public function findUser($email, $phone)
	{
		$email=strtolower($email);
		
		$user=$this->findByName($email);
		
		
		if($user) return $user;

		return $this->findByName($phone);
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
		$sid=strtoupper($sid);
		$profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return User::find($profile->userId);
	}

	public function getBySIDs(array $sids)
	{
		$sids=array_map(function($sid){
			return strtoupper($sid);
		}, $sids);
		
		$userIds=Profile::whereIn('sid',$sids)->pluck('userId')->toArray();
		
		return $this->getByIds($userIds);
       
	}
	
	public function addRole(User $user ,string $roleName)
	{
		$role=Role::getByName($roleName);
		
		$user->roles()->attach($role);
	}
	public function getContactInfo(User $user)
	{
		return $user->contactInfoes->first();
	}
	public function setContactInfo(User $user, ContactInfo $contactInfo , Address $address)
	{
		$exist=$this->getContactInfo($user);
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
	
	public function validateUserInputs(array $values, string $roleName)
	{
		
		$needSID=false;
		$needDOB=false;
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
		if(!$email){
			//沒有email
			if(!$phone){
				$errors['user.phone'] = ['必須填寫手機'];
			}else{
				$values['name']=$phone;
				$existUser=$this->findByName($values['name']);
				if($existUser && $existUser->id!=$id){
					$errors['user.phone'] = ['手機號碼重複了'];
				} 
			}
			
		}else{
			$values['name']=$email;
			$existUser=$this->findByName($values['name']);
			if($existUser && $existUser->id!=$id){
				$errors['user.email'] = ['Email重複了'];
			} 
			
		}

		if($needFullname){
			$fullname=$values['profile']['fullname'];
			if(!$fullname) $errors['user.profile.fullname'] = ['必須填寫姓名'];
				
		}

		$sid=$values['profile']['sid'];

		if($sid){
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