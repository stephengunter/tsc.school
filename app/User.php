<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Profile;
use App\ContactInfo;
use App\Address;
use App\Role;
use App\Account;
use App\Identity;
use Carbon\Carbon;
use App\Core\Helper;
use DB;

class User extends Authenticatable
{
    public static $snakeAttributes = false;
    
    use Notifiable;
    protected $fillable = [
        'name', 'email', 'password','phone','updatedBy'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function init()
	{
		return [
			'name' => '',
			'email' => '',
			'phone' => '',
			'password' => '',
			

			'profile'=> Profile::init()

		];
    }
    
    public function sendPasswordResetNotification($token)
    {
        dispatch(new \App\Jobs\SendResetPasswordMail($this,$token));  
       
    }

    public function profile() 
	{
		return $this->hasOne(Profile::class,'userId');
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_user','user_id','role_id');
    }

    public function contactInfoes() 
	{
		return $this->hasMany('App\ContactInfo','userId');
    }

    public function accounts() 
	{
		return $this->hasMany('App\Account','userId');
    }
    
    public function admin() 
	{
		return $this->hasOne(Admin::class,'userId');
    }

    public function teacher() 
	{
		return $this->hasOne(Teacher::class,'userId');
    }

    public function volunteer() 
	{
		return $this->hasOne(Volunteer::class,'userId');
    }

    public function signups() 
	{
		return $this->hasMany('App\Signup','userId');
    }
    
    public function students() 
	{
		return $this->hasMany('App\Student','userId');
    }

    public function payrolls() 
	{
		return $this->hasMany('App\Payroll','userId');
    }
    

    public function identities()
    {
        return $this->belongsToMany(Identity::class,'identity_user','user_id','identity_id');
    }

    public function getDefaultPassword(Profile $profile=null)
	{
        if(!$profile) $profile=$this->profile;
		if(!$profile) return config('app.user.default_pw');
		if ($profile->dob){
			$dob=new Carbon($profile->dob);
			return Helper::toTaipeiDateString($dob);
		} 
		return config('app.user.default_pw');
	}

    public function setPasswordAttribute($value) 
    {
       
        if(!$value){
            $value = $this->getDefaultPassword();
        }
		$this->attributes['password'] = bcrypt($value);
    }

    public function setNameAttribute($value) 
    {
        if($this->profile) $this->attributes['name'] = strtoupper($this->profile->sid);
		else $this->attributes['name'] = $value;
    }
   

    public function hasRole(string $roleName)
    {
        
        $hasRoleNames=$this->roles->pluck('name')->toArray();
       
        return in_array( $roleName,$hasRoleNames);
    }

    public function isDev()
    {
        return $this->hasRole(Role::devRoleName());
    }
    public function isBoss()
    {
        return $this->hasRole(Role::bossRoleName());
    }
    public function isStaff()
    {

        return $this->hasRole(Role::staffRoleName());
    }
    public function isTeacher()
    {
      
        return $this->hasRole(Role::teacherRoleName());
    }
    public function isStudent()
    {
        return $this->hasRole(Role::studentRoleName());
    }
    
    public function getContactInfo()
	{
		return $this->contactInfoes->first();
    }
    public function setContactInfo(array $contactInfoValues , array $addressValues)
	{
		$exist=$this->getContactInfo();
		if($exist){
			$exist->address->update($addressValues);
			$exist->update($contactInfoValues);
		}else{
			DB::transaction(function() use($contactInfoValues,$addressValues) {
                $contactInfoValues['userId'] = $this->id;
                $contactInfo =ContactInfo::create($contactInfoValues);
				$contactInfo->address()->save(new Address($addressValues));
			});
		}
    }

    
    
	public function loadContactInfo()
	{
		$this->contactInfo=$this->getContactInfo();
		if($this->contactInfo)  $this->contactInfo->address->fullText();
    }

    public function getAge($date=null)
    {
        return $this->profile->getAge($date);
    }

    public function loadIdentityNames()
    {
        if(count($this->identities)){
            $this->identityNames=join(',',$this->identities->pluck('name')->toArray());
        }else{
            $this->identityNames='';
        }
    
    } 

    public function updateProfile(array $profileValues)
    {
        $sid=strtoupper($profileValues['sid']);
        $profileValues['gender'] = Helper::getGenderFromSID($sid);
        $this->profile->update($profileValues);
    }

    public function getAccount()
	{
		return $this->accounts->first();
    }
    public function setAccount(Account $account)
	{
        $this->accounts()->delete();
        $this->accounts()->save($account);
	}
    
    public function addRole(string $roleName)
	{
        if($this->hasRole($roleName)) return;
        $role=Role::getByName($roleName);
       
		
		$this->roles()->attach($role->id);
    }
    
    public function removeRole(string $roleName)
	{
        if(!$this->hasRole($roleName)) return;
		$role=Role::getByName($roleName);
		
		$this->roles()->detach($role->id);
    }

    public function hasIdentity($identityId)
    {
        $identityId=(int)$identityId;
        $hasIdentityIds=$this->identities->pluck('id')->toArray();
        return in_array( $identityId,$hasIdentityIds);
    }
    
    public function addIdentity($identityId)
	{
        if($this->hasIdentity($identityId)) return;
		$this->identities()->attach($identityId);
    }

    public function removeIdentity($identityId)
	{
        if(!$this->hasIdentity($identityId)) return;
		$this->identities()->detach($identityId);
    }

    public function  toOption()
    {
        return [ 'text' => $this->profile->fullname ,  'value' => $this->id ];
    }

    public function loadViewModel()
    {
      
        $this->loadIdentityNames();
    }

}
