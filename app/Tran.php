<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tran extends Model
{
    protected $fillable = [
        'date', 'signupDetailId', 'courseId' , 'tuition',
        'ps', 'updatedBy'
    ];

    public static function init()
	{
		return [
			'date' =>Carbon::today()->toDateString(),
			'signupDetailId' => 0,
			'courseId' => 0,
			'tuition' => 0,
			'ps' => '',
            'isPay' => true,
            'studentId' => 0,
			'updatedBy' => ''

		];
	}

    public function signupDetail() 
	{
		return $this->hasOne('App\SignupDetail', 'id' ,'signupDetailId');
    }

    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
    }
}
