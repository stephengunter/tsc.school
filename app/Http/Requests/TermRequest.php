<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
{
   
    public function authorize()
    {
        return true;
    }

   
    public function rules()
    {
       
        return [
            'term.openDate' => 'required',
            'term.birdDate' => 'required',
            'term.closeDate'=> 'required', 
        ];
    }

    public function messages()
    {
        return [
            'term.openDate.required' => '必須填寫報名起始日',
            'term.birdDate.required' => '必須填寫早鳥優惠截止日',
            'term.closeDate.required' => '必須填寫報名截止日',     
        ];
       
    }

    public function getValues()
    {
       
        $values=array_except($this->get('term'), ['canDelete']); 
        $values['number']=$values['year'] . $values['order'];
        
        if(!$values['name'])    $values['name']=$values['number'];
      
        return  $values;
        
        
    }

}
