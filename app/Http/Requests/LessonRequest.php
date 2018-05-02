<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('lesson');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function rules()
    {
       

        return [
            'lesson.date' => 'required',
          
        ];
    }
    public function messages()
    {
        return [
            'lesson.date.required' => '請填寫日期',
            
        ];
    }

    public function getValues()
    {
        $values= $this->get('lesson');
        $values= array_except($values, ['members','course','teachers','teacherGroup','volunteers']);
        return $values;
      
    }

    public function getTeacherIds()
    {
        $values=$this->get('teacherIds');
        return $values;
    }

    public function getVolunteerIds()
    {
        $values=$this->get('volunteerIds');
        return $values;
    }

  
}
