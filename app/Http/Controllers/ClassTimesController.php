<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\ClassTime;
use App\Services\CourseInfoes;
use App\Services\Courses;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class ClassTimesController extends Controller
{
    
    public function __construct(Courses $courses,CourseInfoes $courseInfoes)
    {
        $this->courses=$courses;
        $this->courseInfoes=$courseInfoes;
      
    }
   

    public function store(Request $request)
    {
        $values=$request->toArray();
       
       
        $on=$values['on'];
        $off=$values['off'];

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        ClassTime::create($values);
        
        return response() ->json();
      
    }
    function validateInputs(array $values)
    {
        $errors=[];
        $on=$values['on'];
        $off=$values['off'];
        
        $on=(int)$on;
        if(!$this->courseInfoes->isValidTimeNumber($on)){
            $errors['on'] = ['時間錯誤'];
        }
        $off=(int)$off;
        if(!$this->courseInfoes->isValidTimeNumber($off)){
            $errors['off'] = ['時間錯誤'];
        }

        if($on >= $off)  $errors['off'] = ['時間錯誤'];

        return $errors;
    }

    public function update(Request $request, $id)
    {
        $classTime=ClassTime::findOrFail($id);

        $values=$request->toArray();
       
      

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        $classTime->update($values);

        return response() ->json();
    }

    

    public function destroy($id) 
    {
        $classTime=ClassTime::findOrFail($id);
        $classTime->delete();
       
       
        return response() ->json();
    }

    public function import(Request $form)
    {
      
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');   

        $err_msg=$this->courseInfoes->importClassTimes($file,$this->currentUserId());
     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
