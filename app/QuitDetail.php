<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SignupDetail;

class QuitDetail extends Model
{
    protected $table = 'quitDetails';

    protected $fillable = [ 'quitId', 'signupDetailId', 
    'percents' , 'tuition' , 'ps' ,'updatedBy'  ];

    public static function init(SignupDetail $signupDetail)
    {
        return [
            'course' => $signupDetail->course,
            'signupDetailId' => $signupDetail->id,
            'percents' => 0,
            'tuition' => '',
            'ps' => ''
        ];
    }

    public function quit() 
	{
		return $this->hasOne('App\Quit', 'id' ,'quitId');
    }

    public function percentsText()
    {
        $percents=(int)$this->percents;
        if($percents==100) return '退全額';
        
        return '退百分之' . $percents;

    }

    public function getSignupDetail()
    {
        return SignupDetail::find($this->signupDetailId);
    }


    public function getCourse()
    {
        return SignupDetail::find($this->signupDetailId)->course;
    }

    public function getSummary()
    {
        $courseName=$this->getCourse()->fullName();
        $tuition=$this->tuition;
        
        return sprintf('%s %d (%s)', $courseName, $tuition, $this->percentsText());
    }

    public function loadViewModel()
    {
        

        $this->percentsText=$this->percentsText();

        $this->course=$this->getCourse();
        $this->course->fullName();
        
    }
}
