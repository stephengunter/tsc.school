<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public static $snakeAttributes = false;

    protected $fillable = [
		'centerId', 'userId', 'wageName',
		'year', 'month','status' ,
		'reviewed' , 'reviewedBy' ,
        'ps', 'updatedBy'
    ];

    public static function init()
	{
		return [
			
			'centerId' => 0,
            'userId' => 0,
            'wageName' => '',
            'year' => 0,
            'month' => 0,
			
			
			'status' => 0,
			'ps' => '',
			'updatedBy' => '',

		];
    }
    
    public function details() 
	{
		return $this->hasMany('App\PayrollDetail','payrollId');
    }
    public function center() 
	{
        return $this->hasOne('App\Center', 'id' ,'centerId');
    }
    public function user() 
	{
        return $this->hasOne('App\User', 'id' ,'userId');
    }

    public function monthString() 
	{
        $this->monthString = $this->year . '年' . $this->month . '月';
        return $this->monthString;
    }

    public function amount()
	{
        $total= 0;
        foreach($this->details as $detail){
            $total+= $detail->getMoney();
        }
       
        return round($total);

    }

    public function loadViewModel()
    {
		$this->center;
        $this->monthString();
        
        $this->amount=$this->amount();
		
	}
}
