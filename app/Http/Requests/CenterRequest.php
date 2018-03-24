<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CenterRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('center');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function rules()
    {
       
        $id=$this->getId();

        return [
            'center.name' => 'required|max:255',
            'center.code' => 'required|unique:centers,code,'. $id, 
        ];
    }
    public function messages()
    {
        return [
            'center.name.required' => '必須填寫名稱',
            'center.code.required' => '必須填寫代碼',
            'center.code.unique' => '代碼與現有中心重複',
        ];
    }

    public function getCenterValues()
    {
        $values=$this->get('center');
        return array_except($values, ['contactInfo']);
        
    }

    public function getContactInfoValues()
    {
        $values=$this->get('center');
        $values= array_only($this->get('center'), ['contactInfo']);
       
        return  array_except($values['contactInfo'], ['address']);
    }

    public function getAddressValues()
    {
        $values=$this->get('center');
        $values= array_only($this->get('center'), ['contactInfo']);
        $values=array_only($values['contactInfo'], ['address']);
        return $values['address'];
    }

    public function isOversea()
    {
        return $this->get('center')['oversea'];
    }
}
