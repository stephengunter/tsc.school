<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class BillRequest extends FormRequest
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
          
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }

    public function getValues()
    {
        return $this->toArray();
    }
    

    

}
