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
    
    public function createUser(User $user,Profile $profile, array $roleNames=[])
    {
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