<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('signup');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getUserId()
    {
        $userId=0;
        $userValues=$this->getUserValues(); 
        if(array_key_exists ( 'id' ,$userValues)){
            $userId=(int)$userValues['id'];
        }  
        return $userId;
    }
    public function rules()
    {
       

        return [
            // 'signup.name' => 'required|max:255',
            // 'signup.number' => 'required|max:255',
            // 'signup.beginDate' => 'required',
            // 'signup.endDate' => 'required',
          
        ];
    }
    public function messages()
    {
        return [
            // 'signup.name.required' => '請填寫名稱',
            // 'signup.number.required' => '請填寫編號',
            // 'signup.beginDate.required' => '請填寫開始日期',
            // 'signup.endDate.required' => '請填寫結束日期',
            
        ];
    }

    public function getSignupValues()
    {
        return $this->get('signup');
        
    }

    public function getSignupDate()
    {
        $values= $this->get('signup');
        return $values['date'];
       
    }

    public function getSignupDetails()
    {
        return $this->getSignupValues()['details'];
       
    }

    public function getUserValues()
    {
        return $this->get('user');
       
    }

    public function getClearUserValues()
    {
        return array_except($this->get('user'), ['profile']);
        
    }

    

  
}
