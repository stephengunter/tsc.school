<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WageRequest extends FormRequest
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
            'small_day' => 'required|numeric',
            'small_night' => 'required|numeric',
            'small_holiday' => 'required|numeric',

            'big_day' => 'required|numeric',
            'big_night' => 'required|numeric',
            'big_holiday' => 'required|numeric',

        ];
    }
    public function messages()
    {
        return [
            'name.required' => '必須填寫名稱',

            'small_day.required' => '請填寫金額',
            'small_day.numeric' => '必須是數字',

            'small_night.required' => '請填寫金額',
            'small_night.numeric' => '必須是數字',

            'small_holiday.required' => '請填寫金額',
            'small_holiday.numeric' => '必須是數字',

            'big_day.required' => '請填寫金額',
            'big_day.numeric' => '必須是數字',

            'big_night.required' => '請填寫金額',
            'big_night.numeric' => '必須是數字',

            'big_holiday.required' => '請填寫金額',
            'big_holiday.numeric' => '必須是數字',
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
