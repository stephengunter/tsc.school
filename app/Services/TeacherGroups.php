<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\TeacherGroup;
use App\Wage;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use DB;
use Excel;

class TeacherGroups 
{
    public function __construct()
    {
        $this->with=['center','courses.classTimes.weekday'];
    }
    public function getAll()
    {
        return TeacherGroup::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {   
        return TeacherGroup::with($this->with)->find($id);
    }

    public function addTeachers(TeacherGroup $group, array $teacherIds)
    {

    }

    
    public function fetchTeacherGroups(Center $center = null,  $keyword = '')
    {
        $teacherGroups=null;
        if($keyword) $teacherGroups=$this->getByKeyword($keyword);
        else $teacherGroups=$this->getAll();

        if($center) $teacherGroups=$teacherGroups->where('centerId',$center->id);

        
        return $teacherGroups;
    }

    public function  getByKeyword($keyword)
    {
       
        return $this->getAll()->where('name' , 'LIKE', '%' .$keyword .'%' );
       
    }

    public function  getByCenter(Center $center)
    {   
        return $this->fetchTeacherGroups($center);
    } 
    public function  options(Center $center)
    {
        $teacherGroups=$this->getByCenter($center)->get();
        return $teacherGroups->map(function ($teacherGroup) {
            return $teacherGroup->toOption();
        })->all();
    }

    public function validateInputs($values)
    {
        $errors=[];

     
		if(!$values['name']) $errors['teacher.name'] = ['必須填寫名稱'];


        return $errors;
    }
    
    
    public function importTeacherGroups($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(10);
            $reader->limitRows(100);
        })->get();

        $teacherGroupList=$excel->toArray()[0];
       
        for($i = 1; $i < count($teacherGroupList); ++$i) {
            $row=$teacherGroupList[$i];

            $center_code=trim($row['center']); 
            $name=trim($row['name']);
            $description=trim($row['description']);
         
            if(!$name){
                continue;
            }
           
            if(!$center_code){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }

           
            $center=Center::where('code',$center_code)->first();
            if(!$center){
                $err_msg .= '中心代碼' . $center_code . '錯誤';
                continue;     
            } 

            
            $values=[
                'name' => $name,
                'description' => $description,
                'centerId' => $center->id,
                'updatedBy' => $updatedBy,
                'active' => true,
                'removed' => false
            ];

            TeacherGroup::create($values);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}