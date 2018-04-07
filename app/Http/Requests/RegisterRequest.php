<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class RegisterRequest extends FormRequest
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
            'name' => 'required|max:255',
            'phone'      => 'required|unique:users,phone',
            'email'      => 'required|email|unique:users,email',
            'password' => 'min:6|required',
            'confirmation' => 'required|same:password'

        ];
    }

    public function messages()
    {
        return [
            'name.required' => '必須填寫使用者名稱',

            'email.email' => 'Email格式不正確',
            'email.required' => '必須填寫Email',
            'email.unique' => 'Email與現存使用者重複',

            'password.required' => '必須填寫密碼',
            'password.min' => '密碼長度不足(最少6位)',

            'confirmation.required' => '必須填寫確認密碼',
            'confirmation.same' => '確認密碼錯誤',

            'phone.required' => '必須填寫手機號碼',
            'phone.unique' => '手機號碼與現存使用者重複',

        ];
    }

    public function getValues()
    {
      
        $values=array_except($this->toArray(), ['confirmation']);
        return $values;
    }
    

    

}
