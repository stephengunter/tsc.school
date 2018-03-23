<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user','user_id','role_id');
    }
    public static function devRoleName()
    {
        return 'Dev';
    }
    public static function teacherRoleName()
    {
        return 'Teacher';
    }

    public static function staffRoleName()
    {
        return 'Staff';
    }

    public static function bossRoleName()
    {
        return 'Boss';
    }

    public static function studentRoleName()
    {
        return 'Student';
    }

    public static function getByName($name)
    {
        return Role::where('name',$name)->first();
    }
}
