<?php

namespace App\Services;
use App\Course;
use App\ClassTime;
use App\Process;
use App\Weekday;
use DB;
use Excel;
use App\Core\Helper;

class CourseInfoes 
{
  

    public function weekdayOptions($emptyText='')
    {
        $weekdays=Weekday::orderBy('val')->get();
        $options = $weekdays->map(function ($item) {
            return $item->toOption();
        })->all();

        if($emptyText) array_unshift($options, ['text' => $emptyText , 'value' =>'0']);
        

        return $options;

    }
   
    // public function setClassTimes(Course $course, array $classTimes)
    // {
       
    //     $course->classTimes()->delete();

    //     $course->classTimes()->saveMany($classTimes);

    // } 

    public function isValidTimeNumber($val)
    {
        $timeString =Helper::toTimeString($val);
      
        $arr=explode(':', $val); 
    
        $arr=explode(':', $timeString); 
        if (count($arr) != 2) return false;

        $hour = (int)$arr[0];
        if ($hour > 23 || $hour < 0) return false; 

        $minute = (int)$arr[1];
        if ($minute > 59 || $minute < 0) return false;

        return true;
    }

    public function validateClassTime($classtime)
    {
        $errors=[];
        $on=(int)$classtime->on;
        $off=(int)$classtime->off;
        if($on<600) $errors['classtime.on'] = ['時間錯誤'];
        if($on>2330) $errors['classtime.on'] = ['時間錯誤'];

        if($off<600) $errors['classtime.off'] = ['時間錯誤'];
        if($off>2330) $errors['classtime.off'] = ['時間錯誤'];

        return $errors;
    }

  
   
    public function importClassTimes($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(10);
            $reader->limitRows(100);
        })->get();

        $classTimeList=$excel->toArray()[0];
       
        for($i = 1; $i < count($classTimeList); ++$i) {
            $row=$classTimeList[$i];

           
            $weekdayValue=(int)trim($row['weekday']);
            $on=(int)trim($row['on']);
            $off=(int)trim($row['off']);

            $location=trim($row['location']);
            $number=trim($row['number']);

          
            if(!$on){
                continue;
            }
            if(!$off){
                continue;
            }
            if(!$number){
                continue;
            }

            $weekday=Weekday::where('val', $weekdayValue)->first();
            if(!$weekday)
            {
                $err_msg .= '星期代碼' . $weekdayValue . '不存在,';
                continue;
            }

            $course=Course::where('number',$number)->first();          
            if(!$course)
            {
                $err_msg .= '課程編號' . $number . '不存在,';
                continue;
            }

            $classTime=new ClassTime([
                'on' => $on,
                'off' => $off,
                'courseId' => $course->id,
                'weekdayId' => $weekday->id,
                'location' => $location,
                'updatedBy' => $updatedBy
            ]);

            $classTime->save();
            
           
        }  //end for  

        return $err_msg;


   }

   public function importProcesses($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(10);
            $reader->limitRows(100);
        })->get();

        $processList=$excel->toArray()[0];
       
        for($i = 1; $i < count($processList); ++$i) {
            $row=$processList[$i];

           
            $courseNumber=trim($row['course']);
            $order=(int)trim($row['order']);
            $title=trim($row['title']);
            $content=trim($row['content']);
            $materials=trim($row['materials']);
         

            $course=null;
            if(!$courseNumber){
                continue;
            }else{
                $course=Course::where('number',$courseNumber)->first();
                if(!$course) {
                    $err_msg .= '課程編號' . $courseNumber . '不存在,';
                    continue;
                } 
            }

           
           

            if(!$order){
                $err_msg .= '順序錯誤' . $courseNumber . ',';
                continue;
            }else{
                $orderExist=Process::where('courseId',$course->id)->where('order',$order)->first();
                if($orderExist){
                    $err_msg .= '順序重複了' . $courseNumber;
                    continue;
                }  
               
        
            }
            
            if(!$title){
                $err_msg .= '標題不可空白' . $courseNumber . ',';
                continue;
            }

         

            $process=new Process([
                'courseId' => $course->id,
                'order' => $order,
                'title' => $title,
                'content' => $content,
                'materials' => $materials,

                'updatedBy' => $updatedBy
            ]);

            $process->save();
            
           
        }  //end for  

        return $err_msg;


   }

   
    
}