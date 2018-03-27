<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseInfoRequest extends FormRequest
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
           'course.tuition' => 'required',
           'course.limit' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'course.tuition.required' => '請填寫學費',
            'course.limit.required' => '請填寫人數上限',
        ];
    }

    public function getValues()
    {
        $values=$this->get('course');
        return $values;
        
    }

   
}
