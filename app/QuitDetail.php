<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SignupDetail;

class QuitDetail extends Model
{
    protected $table = 'quitDetails';

    protected $fillable = [ 'signupId', 'signupDetailId', 
    'percents' , 'tuition' , 'ps' ,'updatedBy'  ];

    

    public function quit() 
	{
		return $this->hasOne('App\Quit', 'signupId' ,'signupId');
    }

    public function percentsText()
    {
        $percents=(int)$this->percents;
        if($percents==100) return '退全額';
        
        return '退百分之' . $percents;

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
