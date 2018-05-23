<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Bill;
use Carbon\Carbon;

class Pay extends Model
{
    protected $fillable = [ 'signupId', 'date', 'amount',
    'paywayId'  ];

    public static function init(Bill $bill)
    {
        return [
            'signupId' => $bill->signupId,
            'amount' =>  $bill->getAmountShorted(),
            'date' =>Carbon::today()->toDateString(),
            'paywayId' => 0
        ];
    }

    public function getDateAttribute($attr) {        
        return Carbon::parse($attr)->format('Y-m-d  h:i'); 
    }

    public function bill() 
	{
		return $this->hasOne('App\Bill', 'signupId' ,'signupId');
    }

    public function payway() 
	{
		return $this->hasOne('App\Payway', 'id' ,'paywayId');
    }

    public function canEdit()
    {
        if($this->payway->auto) return false;
        return true;
    }

    public function getCenter()
    {
        return $this->bill->signup->getCenter();
    }
    
}
