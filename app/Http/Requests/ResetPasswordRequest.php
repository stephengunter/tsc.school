<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

  
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'min:6|required',
            'password_confirmation' => 'required|same:password'
        ];
    }
    public function messages()
    {
        return [
            'email.email' => 'Email格式不正確',
            'email.required' => '必須填寫Email',

           
            'password.required' => '必須填寫密碼',
            'password.min' => '密碼長度不足(最少6位)',
            
            'password_confirmation.required' => '必須填寫確認密碼',
            'password_confirmation.same' => '確認密碼與密碼不相符',
        ];
    }
}
