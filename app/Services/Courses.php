<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Category;
use App\TeacherGroup;
use App\Teacher;
use App\Volunteer;
use App\CoursesTeachers;
use App\Profile;
use App\Term;
use App\Center;
use App\Course;
use App\Wage;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use App\Services\Teachers;
use App\Services\Volunteers;
use App\Services\Categories;
use DB;
use Carbon\Carbon;
use Excel;
use App\Core\Helper;

use App\Events\CourseShutDown;

class Courses 
{
    public function __construct(Users $users, Teachers $teachers, Volunteers $volunteers,
                                Categories $categories)
    {
        $this->users=$users;
        $this->teachers=$teachers;
        $this->volunteers=$volunteers;
        $this->categories=$categories;
      
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
    
    public function getByTerm($termId)
    {   
        return $this->getAll()->where('termId',$termId);
    } 

    public function createCourse(Course $course,Array $categoryIds,Array $teacherIds=[],Array $volunteerIds=[])
    {
        $course->categoryId=$categoryIds[0];
        $course->save();
        $course->categories()->attach($categoryIds);

        if(count($teacherIds)){
            $course->teachers()->attach($teacherIds);
            foreach($teacherIds as $teacherId){
                $teacher=Teacher::find($teacherId);
                if($teacher) $teacher->addToCenterById($course->centerId);
            }
        } 
        if(count($volunteerIds)){
            $course->volunteers()->attach($volunteerIds);
            foreach($volunteerIds as $volunteerId){
                $volunteer=Volunteer::find($volunteerId);
                if($volunteer) $volunteer->addToCenterById($course->centerId);
            }
        } 

        return $course;
        
    }

    public function  updateCourse(Course $course ,Array $categoryIds,Array $teacherIds=[],Array $volunteerIds=[])
    {
        $course->save();
        $course->categories()->sync($categoryIds);

        $course->teachers()->sync($teacherIds);
        $course->volunteers()->sync($volunteerIds);
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

    //多筆課程狀態變動
    public function setActives(array $ids, $active, $reviewedBy)
    {
        $courses=Course::where('removed',false)->whereIn('id',$ids)->get();
       
        foreach($courses as $course){
            if($course->hasStarted()) continue;
            if(!$active){
                $percents=100;
                $this->shutDownCourse($course, $percents , $reviewedBy);
            }else{
           
                $course->active=$active;
                $course->reviewedBy=$reviewedBy;
                $course->updatedBy=$reviewedBy;
                $course->save();
            }
            

        } 

    }
    //單筆課程狀態重新開啟課程
    public function setActive(Course $course, $active ,$reviewedBy, $percents=100)
    {
     
        if(!$active){
            
            $this->shutDownCourse( $course, $percents,$reviewedBy);
            
        }else{
            $course->active= true;//$active;
            $course->reviewedBy=$reviewedBy;
            $course->updatedBy=$reviewedBy;
            $course->save();
            
        }  

    }

    public function  shutDownCourse(Course $course, $percents,$reviewedBy )
    {
        $course->active= false;
        $course->reviewedBy=$reviewedBy;
        $course->updatedBy=$reviewedBy;
        $course->save();

        event(new CourseShutDown($course,$percents));
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

    public function  getStartedCourses(Term $term, Carbon $date=null)
    {
        $courses=$this->getByTerm($term->id)->get();
        $courses = $courses->filter(function ($course) use($date){
            return $course->hasStarted($date);
        })->all();
        
        return $courses;
                        
    }

    public function  getProcessingCourses(Term $term, Carbon $date=null)
    {
        
        $courses=$this->getByTerm($term->id)->get();
        $courses = $courses->filter(function ($course) use($date) {
            return $course->isProcessing($date);
        })->all();
        
        return $courses;
                        
    }

    public function  getByNumber($number)
    {
        return Course::where('number', $number)->first();
    }

    public function  getByKeyword($keyword,bool $includStudents = false)
    {
        $byNames=Course::where('name', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byNumbers=Course::where('number', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();

        $byTeacherUserIds=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $byTeachers = CoursesTeachers::whereIn('teacher_id',$byTeacherUserIds)->get()->pluck('course_id')->toArray();
       
        $courseIds=array_unique(array_merge($byNames,$byNumbers,$byTeachers));

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
            $reader->limitColumns(20);
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

            $categoryCode=trim($row['categories']);
            $teacherSIDs= trim($row['teachers']);  
            $teacherGroupId= trim($row['group']);
          
            $volunteerSIDs= trim($row['volunteers']);

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
            if(!$categoryCode){
                $err_msg .= '課程分類不可空白' . ',';
                continue;
            }else{
                $category=$this->categories->getCategoryByCode($categoryCode);
                if(!$category){
                    $err_msg .= '課程分類錯誤' . ',';
                    continue;
                }
            }

            $teacherIds=[];
            if($teacherSIDs){
                $teacherSIDs = explode(',', $teacherSIDs);  
                $teachers=$this->teachers->getBySIDs($teacherSIDs);
                $teacherIds=$teachers->pluck('userId')->toArray();
            } 

            $volunteerIds=[];
            if($volunteerSIDs){
                $volunteerSIDs = explode(',', $volunteerSIDs);  
                $volunteers=$this->volunteers->getBySIDs($volunteerSIDs);
                $volunteerIds=$volunteers->pluck('userId')->toArray();
            } 
            
           

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

            $course=$this->createCourse($course,[$category->id],$teacherIds,$volunteerIds);

            
            
           
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