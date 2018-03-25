<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('teacher');
       
        $id=0;        
        if(array_key_exists ( 'userId' ,$values)){
            $id=(int)$values['userId'];
        }  
        return $id;
    }
    public function getUserId()
    {
        $values = $this->get('user');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }

    public function rules()
    {
       return [];
        
    }
    public function messages()
    {
        return [];
    }

    public function getTeacherValues()
    {
        $values=$this->get('teacher');
        return array_except($values, ['user','centers']);
        
    }
    public function getUserValues()
    {
        return $this->get('user');
        
    }

    public function getCenterIds()
    {
        $values=$this->get('centerIds');
       
        return $values;
    }

    public function getRole()
    {
        return $this->get('role');
    }
}
