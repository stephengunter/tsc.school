<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
   
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('account');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }

   
   
    public function rules()
    {
        return [
            'account.number' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'account.number.required' => '必須填寫帳號',
        ];
    }

    public function getValues()
    {
        return $this->get('account');
        
        
    }

}
