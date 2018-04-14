<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Profile;
use App\Role;
use App\Identity;

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

    public function wages() 
	{
		return $this->hasMany('App\Wage','userId');
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
		return $this->hasMany('App\Students','userId');
    }
    public function identities()
    {
        return $this->belongsToMany(Identity::class,'identity_user','user_id','identity_id');
    }
    public function setPasswordAttribute($value) 
    {
		$this->attributes['password'] = bcrypt($value);
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
	public function loadContactInfo()
	{
		$this->contactInfo=$this->getContactInfo();
		if($this->contactInfo)  $this->contactInfo->address->fullText();
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

}
