<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInfoRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('contactInfo');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function rules()
    {

        return [ ];
           
       
    }
    public function messages()
    {
        return [ ];
       
    }

    public function getContactInfoValues()
    {
        $values=$this->get('contactInfo');
        return  array_except($values, ['address']);
    }
    

    public function getAddressValues()
    {
        $values=$this->get('contactInfo');
        $values=array_only($values, ['address']);
        return array_except($values['address'],['district']);
    }
}
