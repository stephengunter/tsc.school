<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'code' => 'required|unique:categories,code,'. $id, 
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '必須填寫名稱',
            'code.required' => '必須填寫代碼',
            'code.unique' => '代碼與現有分類重複',
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
