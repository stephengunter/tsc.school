<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuitRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function getId()
    {
        $values = $this->get('quit');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
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

    public function getQuitValues()
    {
        return $this->get('quit');
        
    }
    public function getQuitDetailValues()
    {
        return $this->get('details');
        
    }
}
