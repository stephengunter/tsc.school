<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolunteerRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('volunteer');
       
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

    public function getVolunteerValues()
    {
        $values=$this->get('volunteer');
        return array_except($values, ['user']);
        
    }
    public function getUserValues()
    {
        return $this->get('user');
        
    }
}
