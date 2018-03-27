<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('course');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function rules()
    {
       

        return [
            'course.name' => 'required|max:255',
            'course.number' => 'required|max:255',
            'course.beginDate' => 'required',
            'course.endDate' => 'required',
          
        ];
    }
    public function messages()
    {
        return [
            'course.name.required' => '請填寫名稱',
            'course.number.required' => '請填寫編號',
            'course.beginDate.required' => '請填寫開始日期',
            'course.endDate.required' => '請填寫結束日期',
            
        ];
    }

    public function getValues()
    {
        return $this->get('course');
        return array_except($values, ['teacherGroup','term','center','classTimes','teachers']);
        
    }

    public function getTeacherIds()
    {
        $values=$this->get('teacherIds');
        return $values;
    }

  
}
