<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Category;
use App\TeacherGroup;
use App\Teacher;
use App\Profile;
use App\Term;
use App\Center;
use App\Course;
use App\Wage;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use DB;
use Excel;
use App\Core\Helper;

class Courses 
{
    public function __construct()
    {
        $this->with=['teacherGroup','term','center','classTimes.weekday'];
    }
    public function getAll(bool $includStudents = false)
    {
        if($includStudents) return Course::with(['teacherGroup','term','center','Students'])->where('removed',false); 
        return Course::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {   
        return $this->getAll()->where('id',$id)->first();
    }
    public function getByIds($ids)
    {   
        return $this->getAll()->whereIn('id',$ids);
    }    

    public function createCourse(Course $course,Array $categoryIds,Array $teacherIds=[])
    {
        $course->categoryId=$categoryIds[0];
        $course->save();
        $course->categories()->attach($categoryIds);

        if(count($teacherIds)) $course->teachers()->attach($teacherIds);

        return $course;
        
    }

    public function  updateCourse(Course $course ,Array $categoryIds,Array $teacherIds=[])
    {
        $course->save();
        $course->categories()->sync($categoryIds);

        if(count($teacherIds)) $course->teachers()->sync($teacherIds);
        else $course->teachers()->delete();
    }

    public function reviewOK(array $ids, $reviewedBy)
    {
        $courses=Course::where('removed',false)->whereIn('id',$ids)->get();
        foreach($courses as $course){
            $course->reviewed=true;
            $course->reviewedBy=$reviewedBy;
            $course->updatedBy=$reviewedBy;
            $course->save();
        } 
    }

    public function  updateReview($id,bool $reviewed,int $reviewedBy)
    {
        $course=Course::find($id);
        $course->reviewed=$reviewed;
        $course->updatedBy=$reviewedBy;

        if($reviewed){
            $course->reviewedBy=$reviewedBy;
        }else{
            $course->reviewedBy='';
        }
        $course->save();
    }

    public function setActives(array $ids, $active, $reviewedBy)
    {
        $courses=Course::where('removed',false)->whereIn('id',$ids)->get();
        foreach($courses as $course){
            $course->active=$active;
            $course->reviewedBy=$reviewedBy;
            $course->updatedBy=$reviewedBy;
            $course->save();
        } 

    }


    public function deleteCourse(Course $course,$updatedBy)
    {
        $course->removed=true;
        $course->updatedBy=$updatedBy;
        $course->save();
        
    }
       
    public function fetchCourses(int $termId,Center $center = null, Category $category = null,bool $reviewed=true,  $keyword = '',bool $includStudents = false)
    {
        $courses=null;
        if($keyword) $courses=$this->getByKeyword($keyword);
        else $courses=$this->getAll();

        $courses = $courses->where('termId',$termId);

        if ($center) $courses = $courses->where('centerId',$center->id);

        if($category){
            $courses=$this->fetchByCategory($courses , $category->id);
        }

        return $courses->where('reviewed',$reviewed)
                       ->orderBy('number');
    }

    public function fetchByCategory($courses , int $categoryId)
    {
        $courses=$courses->whereHas('categories', function($q) use ($categoryId)
        {
            $q->where('id',$categoryId );
        });
        return  $courses;
    }

    public function  getByNumber($number)
    {
        return Course::where('number', $number)->first();
    }

    public function  getByKeyword($keyword,bool $includStudents = false)
    {
        $byNames=Course::where('name', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byNumbers=Course::where('number', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
       
        $courseIds=array_unique(array_merge($byNames,$byNumbers));

        return $this->getAll($includStudents)->whereIn('id' , $courseIds );
       
    }

    public function getCourseByNumber($number)
    {
        return $this->getAll()->where('number',$number)->first();
    }

    public function  options(Term $term, Center $center,bool $withEmpty=false)
    {
        $courses=$this->fetchCourses($term->id,$center)->get();
        $options =  $courses->map(function ($course) {
            return $course->toOption();
        })->all();

        if($withEmpty) array_unshift($options, ['text' => '所有課程' , 'value' =>'0']);
        
        return $options;
       
    }
    
    public function importCourses($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $courseList=$excel->toArray()[0];
       
        for($i = 1; $i < count($courseList); ++$i) {
            $row=$courseList[$i];

           
            $number=trim($row['number']);
            $name=trim($row['name']);
            $level=trim($row['level']);

            $center_code=trim($row['center']);
            $termNumber=(int)trim($row['term']);

            $categoryId=(int)trim($row['categories']);
            $teacherIds= trim($row['teachers']);  
            $teacherGroupId= trim($row['group']);
           

            $begin_date=trim($row['begin_date']);
            $end_date=trim($row['end_date']);
            $description=trim($row['description']);

            $hours=(int)trim($row['hours']);
            $weeks=(int)trim($row['weeks']);

            if(!$name){
                continue;
            }

            $term=null;
            if(!$termNumber){
                $err_msg .= '學期不可空白' . ',';
                continue;
            }else{
                $term=Term::where('number',$termNumber)->first();
                if(!$term){
                    $err_msg .= '學期'. $termNumber .'錯誤' . ',';
                    continue;
                }
            }


            $center=null;
            if(!$center_code){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }else{
                $center=Center::where('code',$center_code)->first();
                if(!$center){
                    $err_msg .= '中心代碼'. $center_code .'錯誤' . ',';
                    continue;
                }
            }

            if(!$number){
                $err_msg .= '編號不可空白' . ',';
                continue;
            }
            if(!$hours){
                $err_msg .= '時數不可空白' . ',';
                continue;
            }
            if(!$weeks){
                $err_msg .= '週數不可空白' . ',';
                continue;
            }
            if(!$begin_date){
                $err_msg .= '開始日期不可空白' . ',';
                continue;
            }
            if(!$end_date){
                $err_msg .= '結束日期不可空白' . ',';
                continue;
            }

            $category=null;
            if(!$categoryId){
                $err_msg .= '課程分類不可空白' . ',';
                continue;
            }else{
                $category=Category::find($categoryId);
                if(!$category){
                    $err_msg .= '課程分類錯誤' . ',';
                    continue;
                }
            }

            if($teacherIds) $teacherIds= explode(',', $teacherIds);  
            else $teacherIds=[];
           

            $teacherGroup=null;
            if($teacherGroupId){
                $teacherGroup=TeacherGroup::find($teacherGroupId);
                if(!$teacherGroup){
                    $err_msg .= '教師群組錯誤' . ',';
                    continue;
                }
            }else{
                
                if(!$teacherIds){
                    $err_msg .= '教師不可空白' . ',';
                    continue;
                }
            }

            

            $courseNumber=Course::initCourseNumber($number ,$category,$center ,$term );
           

            $existCourse = $this->getByNumber($courseNumber);
            if($existCourse)
            {
                $err_msg .= '課程編號重複' . $number .  ',';
                continue;
            }

            $course=new Course([
                'name' => $name,
                'level' => $level,
                'number' => $courseNumber,
                'discount' => 1,
            
                'centerId' => $center->id,
                'termId' => $term->id,
            
                'weeks' => $weeks,
                'hours' => $hours,
                'beginDate'=> $begin_date,
                'endDate'=> $end_date,

                'description' => $description,
                'net'=> 1 ,
                'active'=> 1 ,
                'updatedBy' => $updatedBy
            ]);

            if($teacherGroup) $course->teacherGroupId=$teacherGroup->id;

            $course=$this->createCourse($course,[$categoryId],$teacherIds);

            
            
           
        }  //end for  

        return $err_msg;


   }

   public function importCourseDetails($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $courseList=$excel->toArray()[0];

        array_shift($courseList);
        $numbers = array_pluck($courseList, 'number');
        $numbers = array_filter($numbers);

        if(Helper::array_has_dupes($numbers))
        {
            return '課程編號有重複';
        }
      
       
        for($i = 0; $i < count($courseList); ++$i) {
            $row=$courseList[$i];

           
            $number=trim($row['number']);
            $tuition=trim($row['tuition']);
            $materials=trim($row['materials']);

            $cost=floatval(trim($row['cost']));
            $caution=trim($row['caution']);

            $limit=(int)trim($row['limit']);
            $min= (int)trim($row['min']);  
            $target= trim($row['target']);

            if(!$number){
                continue;
            }
            $course= $this->getByNumber($number);
            if(!$course)
            {
                $err_msg .= '課程編號' . $number . '不存在,';
                continue;
            }
          
            if(!$tuition){
                $err_msg .= '學費不可空白' . ',';
                continue;
            }else{
                $tuition=floatval($tuition);
                if(!$tuition){
                    $err_msg .= '學費錯誤' . ',';
                    continue;
                }
            }

           
            $course->update([
                'tuition' => $tuition,
                'materials'=> $materials,
                'cost'=> $cost,
                'caution'=> $caution,
                'limit'=> $limit,
                'min'=> $min,
                'target'=> $target
            ]);

            
            
           
        }  //end for  
        
        return $err_msg;


   }
    
}