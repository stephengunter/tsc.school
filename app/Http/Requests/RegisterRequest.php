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
            'fullname' => 'required|max:255',
            'sid' => 'required|max:255',
            'dob' => 'required|max:255',
            'phone'      => 'required',
            'email'      => 'required|email',
            'password' => 'min:6|required',
            'confirmation' => 'required|same:password'

        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => '必須填寫姓名',
            'sid.required' => '必須填寫身分證號',
            'dob.required' => '必須填寫生日',

            'email.email' => 'Email格式不正確',
            'email.required' => '必須填寫Email',

            'password.required' => '必須填寫密碼',
            'password.min' => '密碼長度不足(最少6位)',

            'confirmation.required' => '必須填寫確認密碼',
            'confirmation.same' => '確認密碼錯誤',

            'phone.required' => '必須填寫手機號碼',

        ];
    }

    public function getValues()
    {
      
        $values=array_except($this->toArray(), ['confirmation']);
        return $values;
    }
    

    

}
