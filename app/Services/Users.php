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


	public function findByName($name)
	{
		User::where('name',$name)->first();

	}

	public function findUser($email, $phone)
	{
		$email=strtolower($email);
		$user=$this->findByName($email);
		if($user) return $user;

		return $this->findByName($phone);
	}

	public function findBySID($sid)
	{
		$sid=strtoupper($sid);
		$profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return User::find($profile->userId);
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
}