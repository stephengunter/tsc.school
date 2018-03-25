<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
   
    public function authorize()
    {
        return true;
    }

   
    public function rules()
    {
       
        return [ ];
          
       
    }

    public function messages()
    {
        return [ ];
    }

    public function getUserValues()
    {
        $values=$this->get('user');
        return array_except($values, ['profile','contact_infoes']);
        
    }

    public function getProfileValues()
    {
        $values=$this->get('user');
        return $values['profile'];
       // return array_only($values, ['profile','centers']);
        
    }

    // public function getValues()
    // {
       
    //     $values=array_except($this->get('term'), ['canDelete']); 
    //     $values['number']=$values['year'] . $values['order'];
        
    //     if(!$values['name'])    $values['name']=$values['number'];
      
    //     return  $values;
        
        
    // }

}
