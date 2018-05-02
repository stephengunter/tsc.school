<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $table = 'payrollDetails';

    protected $fillable = [ 'payrollId', 'lessonId', 
        'date' , 'on' , 'off' ,'minutes'  ,
        'studentCount','wageName','wageMoney'
    ];

    

    public function payroll() 
	{
		return $this->hasOne('App\Payroll', 'id' ,'payrollId');
    }
}
