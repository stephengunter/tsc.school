<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tran;

class Signup extends Model
{
    public static $snakeAttributes = false;
	
    protected $fillable = [  
                            'userId', 'net', 'points', 'discount' ,
                            'tuitions', 'costs', 'status','identity_ids',
                            'updatedBy', 'removed','ps'
                          ];
                          
                         

	public static function init()
	{
		return [
			'userId' => 0,
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
    
    public function amount()
	{
        $total= $this->tuitions + $this->costs;
        
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


    public function getDate()
	{
        $this->date = $this->created_at->format('Y-m-d');
        return $this->created_at;
    }

    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }
    
    public function bill() 
	{
		return $this->hasOne(Bill::class,'signupId');
    }
    public function quits() 
	{
		return $this->hasMany(Quit::class,'signupId');
    }

    

    public function  canDelete()
    {
        if(!$this->canAddDetail()) return false;
        if(count($this->quits)) return false;

        return true;
    }

    public function canAddDetail()
    {
       
        if($this->hasCanceled()) return false;
        if($this->bill->code) return false;
        if($this->bill->getPaysTotalMoney() > 0) return false;

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
        $this->bill->updateStatus();
      
        foreach($this->details as $detail){
            $detail->updateStatus();
        }

        $validDetail=$this->details()->where('canceled',false)->first();

        if($validDetail){
            if($this->bill->payed) $this->status=1;
            else $this->status=0;

        }else{
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
        return $this->bill->payed;
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

        $this->bill->loadViewModel();


        
    }

    

}
