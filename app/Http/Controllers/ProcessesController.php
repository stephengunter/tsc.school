<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Course;
use App\Process;
use App\Services\CourseInfoes;
use App\Services\Courses;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class ProcessesController extends Controller
{
    
    public function __construct(Courses $courses,CourseInfoes $courseInfoes)
    {
        $this->courses=$courses;
        $this->courseInfoes=$courseInfoes;
      
    }
    
    public function store(Request $request)
    {
        $values=$request->toArray();

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        Process::create($values);
        
        return response() ->json();
      
    }
    function validateInputs(array $values)
    {
        $errors=[];

        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  

        $courseId=$values['courseId'];
        $order=(int)$values['order'];
        $title=$values['title'];

        if(!$title)   $errors['title'] = ['請填寫標題'];

        $orderExist=Process::where('courseId',$courseId)->where('order',$order)->first();
        if($orderExist && $orderExist->id!=$id)   $errors['order'] = ['順序重複了'];

        return $errors;
    }

    public function update(Request $request, $id)
    {
        $process=Process::findOrFail($id);

        $values=$request->toArray();

        $errors=$this->validateInputs($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy']=$this->currentUserId();
        $process->update($values);

        return response() ->json();
    }

    

    public function destroy($id) 
    {
        $process=Process::findOrFail($id);
        $process->delete();
       
       
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

        $err_msg=$this->courseInfoes->importProcesses($file,$this->currentUserId());
     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
