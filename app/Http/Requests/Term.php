<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class Term extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'term.openDate.required' => '請填寫報名起始日',
            'term.birdDate.required' => '請填寫早鳥截止日', 
            'term.closeDate.required' => '請填寫開課決定日',
            
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

    public function getValues($updated_by,$removed)
    {
       
        $values=array_except($this->get('term'), ['canDelete']); 
        $values['number']=$values['year'] . $values['order'];
        
        if(!$values['name'])    $values['name']=$values['number'];
      
        $values= static::setUpdatedBy($values,$updated_by);
        
        
    }
}
