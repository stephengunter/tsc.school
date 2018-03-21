<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public static function setUpdatedBy($values, $user_id)
    {
        if (array_key_exists('updatedBy', $values)) {
            $values['updatedBy']=$user_id;
        }else{
            $values=array_add($values, 'updatedBy', $user_id);
        }

        return $values;
    }
}
