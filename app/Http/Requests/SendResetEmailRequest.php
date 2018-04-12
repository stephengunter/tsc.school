<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SendResetEmailRequest extends FormRequest
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
            'email' => 'required|max:255',

        ];
    }

    public function messages()
    {
        return [
            'email.required' => '必須填寫Email',

        ];
    }

   

    

}
