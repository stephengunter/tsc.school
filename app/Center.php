<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ContactInfo;
use App\Core\Helper;
use DB;

class Center extends Model
{
	public static $snakeAttributes = false;
	
	protected $fillable = [  'head', 'key', 'areaId','importance',
							 'name','code', 'courseTel','rule',
							 'active','removed', 'updatedBy'
						  ];

	public static function init()
	{
		return [
			'head' => 0,
			'key' => 'west',
			'areaId' => 0,
			'name' => '',
			'code' => '',
			'courseTel' => '',
			'importance' => 0,
			'rule' => '',
			'active' => 0,

			'contactInfo'=> ContactInfo::init()

		];
	}	

    public function contactInfoes() 
	{
		return $this->hasMany('App\ContactInfo','centerId');
	}

	public function courses() 
	{
		return $this->hasMany('App\Course','centerId');
	}

	
	public function notices() 
	{
		return $this->hasMany('App\Notice','centerId');
	}

	public function area() 
	{
		return $this->hasOne('App\Area', 'id' ,'areaId');
	}

	public function admins()
    {
        return $this->belongsToMany(Admin::class,'center_admin','center_id','admin_id');
	}

	public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'center_teacher','center_id','teacher_id');
	}
	public function teacherGroups()
    {
		return $this->hasMany('App\TeacherGroup','centerId', 'id');
      
	}
	public function discounts()
    {
        return $this->belongsToMany(Discount::class,'center_discount','center_id','discount_id');
	}

	public function payrolls() 
	{
		return $this->hasMany('App\Payroll','centerId');
	}

	public function getContactInfo()
	{
		return $this->contactInfoes->first();
    }
    public function setContactInfo(array $contactInfoValues , array $addressValues)
	{
		
		$exist=$this->getContactInfo();
		if($exist){
			$exist->address->update($addressValues);
			$exist->update($contactInfoValues);
		}else{
			DB::transaction(function() use($contactInfoValues,$addressValues) {
                $contactInfoValues['centerId'] = $this->id;
                $contactInfo =ContactInfo::create($contactInfoValues);
				$contactInfo->address()->save(new Address($addressValues));
			});
		}
	}
	
	public function loadViewModel()
    {
        $this->loadContactInfo();
        $this->keyText=$this->keyText();
    }
    
	public function loadContactInfo()
	{
		$this->contactInfo=$this->getContactInfo();
		if($this->contactInfo)  $this->contactInfo->address->fullText();
    }

	
	public function  toOption()
    {
		return [ 'text' => $this->name ,  'value' => $this->id ];
       
	}

	public function keyText()
	{
		if($this->key=='east') return '東部';
		if($this->key=='west') return '西部';
		if($this->key=='oversea') return '海外';
		return '';
	}
	
	public function isEast()
	{
		return $this->key=='east';
	}

	public function isWest()
	{
		return $this->key=='west';
	}

	public function isOversea()
	{
		return $this->key=='oversea';
	}
}
