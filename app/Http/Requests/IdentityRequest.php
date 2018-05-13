<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdentityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        $id=$this->getId();

        return [
            'name' => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '必須填寫名稱',
        ];
    }
    public function getId()
    {
        $values = $this->getValues();
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getValues()
    {
        return $this->toArray();
    }
}
