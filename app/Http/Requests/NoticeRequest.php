<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoticeRequest extends FormRequest
{
   
    public function authorize()
    {
        return true;
    }

   
    public function rules()
    {
       
        return [
            'notice.date' => 'required',
            'notice.title' => 'required',
            'notice.content'=> 'required', 
        ];
    }

    public function messages()
    {
        return [
            'notice.date.required' => '必須填寫日期',
            'notice.title.required' => '必須填寫標題',
            'notice.content.required' => '必須填寫內容',     
        ];
       
    }

    

}
