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
             'name' => 'required',
             'password' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '請輸入身分證號',
            'password.required' =>'請輸入密碼',
        ];
    }
}
