<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('tran');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getStudentId()
    {
        return $this->getTranValues()['studenId'];
    }

    public function rules()
    {
        return [
            'tran.tuition' => 'required|numeric'
        ];
        
    }
    public function messages()
    {
        return [
            'tran.tuition.required' => '請填寫金額',
            'tran.tuition.numeric' => '必須是數字'
            
        ];
    }

    public function getTranValues()
    {
        return $this->get('tran');
        
    }
    public function isPay()
    {
        return $this->getTranValues()['isPay'];
        
    }
}
