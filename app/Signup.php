<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Bill;
use App\Tran;
use App\Discount;
use Carbon\Carbon;

class Signup extends Model
{
    public static $snakeAttributes = false;
	
    protected $fillable = [  
                            'date',
                            'userId', 'net', 'points', 'discount' ,'discountId' ,
                            'tuitions', 'costs', 'status','identity_ids',
                            'payed',
                            'updatedBy', 'removed','ps'
                          ];
                          
                         

	public static function init()
	{
		return [
            'userId' => 0,
            'date' => Carbon::today()->toDateString(),
			'net' => 0,
			'points' => 0,
			'tuitions' => 0,
			'costs' => 0,
            'discount' => '',
            'status' => 0,
            'identity_ids' => '',
            'ps' => '',
            'details' => []

		];
	}	

    public function details() 
	{
		return $this->hasMany('App\SignupDetail','signupId');
    }
    public function bills() 
	{
		return $this->hasMany('App\Bill','signupId');
    }

    public function unPayedBills()
    {
        return $this->bills()->where('payed',false);
    }
    
    
    public function amount()
	{
        $total= $this->tuitions + $this->costs;
        
        return $total;

    }

    public function initBill($amount=0)
    {
        if($this->payed) return;

        if(!$amount) $amount= $this->getAmountShorted();
        if($amount <= 0) return;

        $existBill = $this->unPayedBills()->first();
        if($existBill){
            $existBill->update([
                'amount' => $amount,
            ]);
            return;
        } 
        
        

        $bill=new Bill([
            'amount' => $amount,
            'payed' => false
        ]);
        $this->bills()->save($bill);
    }

    public function getPayDate()
    {
        return $this->bills()->orderBy('payDate','desc')->first()->payDate;
    }

    public function getAmountShorted()
    {
        $amount=$this->amount();
        $moneyPayed=$this->getPaysTotalMoney();
        return $amount - $moneyPayed;
    }

    public function getPaysTotalMoney()
    {
        $total=0;
        foreach($this->bills as $bill){
            if($bill->payed) $total += $bill->amount;
        }

        return $total;
    }
    
    public function getCenter()
	{
        if(!count($this->details)) return null;

        return $this->details->first()->course->center;
    }

    public function getTerm()
	{
        if(!count($this->details)) return null;

        return $this->details->first()->course->term;
    }

    public function getDiscount()
	{
        if(!$this->discountId) return null;

        return Discount::find($this->discountId);
        
    }


    public function getDate()
	{
        if(!$this->date)  $this->date = $this->created_at->format('Y-m-d');
       
        return $this->date;
    }

    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }
    
    
    public function quits() 
	{
		return $this->hasMany(Quit::class,'signupId');
    }

    

    public function  canDelete()
    {
        if($this->payed) return false;
        if(count($this->quits)) return false;

        return true;
    }

    public function canAddDetail()
    {
        if($this->hasCanceled()) return false;

        $moneyPayed=$this->getPaysTotalMoney();
        if($moneyPayed>0) return false;

        return true;
    }
    public function  canQuit()
    {
        return $this->status == 1;
    }

    public function updateMoney()
    {
        $tuitions = 0;
        foreach($this->details as $signupDetail){
            $tuitions += $signupDetail->tuition;
        } 

        $points= $this->getPoints();
        $this->tuitions = $tuitions * $points / 100;

        $costs = 0;
        foreach($this->details as $signupDetail){
            if($signupDetail->cost)  $costs += $signupDetail->cost;
        }

        $this->costs=$costs;
        $this->save();


    }

    public function updateStatus()
    {
       
        foreach($this->details as $detail){
            $detail->updateStatus();
        }
        
        $this->updateMoney();
      
        
        $validDetail=$this->details()->where('canceled',false)->first();

        if($validDetail){
            $amountShorted=$this->getAmountShorted();

            if($amountShorted > 0){
                $this->payed=false;
                $this->status=0;

                $this->initBill($amountShorted);
            }
            else{
                $this->payed=true;
                $this->status=1;
            }  

        }else{
            //所有報名課程已取消
            $this->status=-1;
        } 

        $this->save();
        

    }

    public function hasDiscount()
    {
        $points=$this->getPoints();
        if($points==100) return false;
        if($points==0) return false;

        return true;
    }

    public function  getValidCoursesCount()
    {
        return $this->details()->where('canceled',false)->count();
    }

    public function getPoints()
    {
        $points=(int)$this->points;
        if(!$points) $points = 100;

        return $points;
    }

    public function pointsText()
    {
        if(!$this->hasDiscount()) return '';

        $points= $this->getPoints();
       
        if($points % 10 == 0){
            return $points/10 . '折';
        }

        return $points . '折';


    }

    public function hasCanceled()
    {
        return $this->status == -1;
    }

    public function hasPayed()
    {
        return $this->payed;
    }
    
    public function getTranRecords()
    {
        $detailIds=$this->details()->pluck('id')->toArray();
        return Tran::whereIn('signupDetailId',$detailIds)->orderBy('date')->get();
    }

    public function loadViewModel()
    {
        $this->getDate();
        $this->amount=$this->amount();

        $this->pointsText=$this->pointsText();

       

        foreach($this->details as $signupDetail){
            $signupDetail->loadViewModel();
        } 

        foreach($this->quits as $quit){
            $quit->loadViewModel();
        } 

        foreach($this->bills as $bill){
            $bill->loadViewModel();
        } 

        $this->amountPayed = $this->getPaysTotalMoney();
        $this->amountShorted = $this->getAmountShorted();


        
    }

    

}
