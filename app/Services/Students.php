<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\Student;
use App\Course;
use App\Services\Users;
use DB;
use Excel;

class Students 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['user.roles','user.profile','user.contactInfoes.address.district.city'];
    }
    
    public function getById($id)
    {   
        return Student::with($this->with)->find($id);
    }

    public function findStudent($courseId, $userId)
    {
        return Student::with($this->with)->where('courseId',$courseId)
                                        ->where('userId',$userId)->first();
    }

    public function removeStudent($courseId, $userId)
    {
        
        $student = $this->findStudent($courseId, $userId);
        
        $student->delete();
        
        $userStudentRecored=Student::where('userId',$userId)->first();
        if(!$userStudentRecored){
            $user=User::find($userId);
            $user->removeRole(Role::studentRoleName());
        }

        return $student;
    }

    public function createStudent($courseId, $userId, $joinDate='')
    {
       
        $exist = $this->findStudent($courseId, $userId);
       
        if ($exist) abort(500);   //重複
        
        $student=Student::create([
            'userId' => $userId,
            'courseId' => $courseId,
            'joinDate' => $joinDate,
            'status' => 1,
            'score' => 0
        ]);
        

        $user=User::find($userId);
        
        $user->addRole(Role::studentRoleName());

        return $student;
    }

    
    
    public function getStudentsByCourse(Course $course)
    {
        return Student::with($this->with)
                       ->where('courseId',$course->id);
                       
    }

    public function  getByKeyword($keyword)
    {
        $byPhones=User::where('phone', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $bySIDs=Profile::where('sid', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
       
        $userIds=array_unique(array_merge($byPhones,$byFullnames,$bySIDs));

        return $this->getAll()->whereIn('userId' , $userIds );
       
    }

    
    
}