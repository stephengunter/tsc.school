<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
   
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('user');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }

    public function getRole()
    {
        $role='';        
        if(array_key_exists ( 'role' ,$this->toArray())){
            $role=$values['role'];
        }  
        return $role;
    }
   
    public function rules()
    {
        return [ ];
    }

    public function messages()
    {
        return [ ];
    }

    public function getUserValues($withProfile=false)
    {
        $values=$this->get('user');
        if($withProfile) return array_except($values, ['contactInfoes']);
        return array_except($values, ['profile','contactInfoes']);
        
        
    }

    public function getProfileValues()
    {
        $values=$this->get('user');
        return $values['profile'];
        
    }

}
