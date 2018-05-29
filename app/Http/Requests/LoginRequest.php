<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class LoginRequest extends FormRequest
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
            'password' => 'required|max:255',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => '必須填寫身分證號',
            'password.required' => '必須填寫密碼',

        ];
    }

    public function getValues()
    {
        return $this->toArray();
    }
    

    

}
