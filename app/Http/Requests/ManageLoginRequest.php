<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
             'email' => 'email|required',
             'password' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => '請輸入Email',
            'email.email' => 'Email格式不正確',
            'password.required' =>'請輸入密碼',
        ];
    }
}
