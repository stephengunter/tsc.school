<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherRequest;

use App\Teacher;
use App\User;
use App\Role;
use App\Services\Teachers;
use App\Services\Courses;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;


use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    
    public function __construct(Teachers $teachers ,Courses $courses)
    {
        $this->teachers=$teachers;
        $this->courses=$courses;
    }

    public function show()
    {
        $id = $this->currentUserId();
        $teacher = $this->teachers->getById($id);
        if(!$teacher) abort(404);

       

        $courseIds=$teacher->courses()->pluck('id')->toArray();
        $courses=$this->courses->getByIds($courseIds)->get();
        foreach($courses as $course){
            $course->fullName();
            $course->loadClassTimes();
            
        } 

        $teacher->courses = $courses;

        $teacher->user->loadContactInfo();

        if($this->isAjaxRequest()){
            return response() ->json($teacher);
        }



        $model=[
            'title' => '教師資訊',
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany(),
            'teacher' => $teacher
        ];

        return view('client.teachers.details')->with($model);
    }

    public function edit()
    {
        $id = $this->currentUserId();
        $teacher = $this->teachers->getById($id);
        if(!$teacher) abort(404);
      
        $form=[
            'teacher' => $teacher
        ];

        return response() ->json($form);

       
        
    }

    public function validateTeacherInputs($values)
    {
        $errors=[];

        $group=false;
        if(array_key_exists('group',$values)) $group=Helper::isTrue($values['group']);
        
        if($group){


        }else{
           

        }

        return $errors;
    }

    public function update(TeacherRequest $request)
    {
        $id = $this->currentUserId();
        $teacher = $this->teachers->getById($id);
        if(!$teacher) abort(404);
       
        $values=$request->getTeacherValues();
     
        $errors=$this->validateTeacherInputs($values);
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $values['updatedBy'] = $id;

        $teacher->update($values);

        return response() ->json();
    }
    
    
   
}
