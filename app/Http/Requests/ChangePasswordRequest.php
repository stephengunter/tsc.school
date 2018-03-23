<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'password' => 'min:6|required',
            'password_confirmation' => 'required|same:password'
        ];
    }
    public function messages()
    {
        return [
          
            'current_password.required' => '必須填寫舊密碼',
            'password.required' => '必須填寫新密碼',
            'password.min' => '密碼長度不足(最少6位)',
            
            'password_confirmation.required' => '必須填寫確認密碼',
            'password_confirmation.same' => '確認密碼與新密碼不相符',
        ];
    }
}
