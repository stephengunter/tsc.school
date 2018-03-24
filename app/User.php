<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Role;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'name', 'email', 'password','phone','updatedBy'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
   

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
    
    public function admin() 
	{
		return $this->hasOne(Admin::class,'userId');
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
		$role=Role::getByName($roleName);
		
		$this->roles()->attach($role);
	}
}
