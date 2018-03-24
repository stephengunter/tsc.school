<?php

namespace App\Services;

use App\Center;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Area;
use DB;
use Excel;

class Centers 
{
    public function __construct()
    {
        $this->with=['area','contactInfoes.address.district.city'];
    }

    public function getAll()
    {
        return Center::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {
        return Center::with($this->with)->find($id);
    }

    public function createCenter(Center $center, ContactInfo $contactInfo=null,Address $address=null)
    {
        if(!$center->importance)
        {
            $min=$this->getMinImportance();

            $center->importance=$min - 1;
        }

        $center->save();

        if($contactInfo) $this->setContactInfo($center,$contactInfo,$address);
        
    }
    
    public function fetchCenters(bool $oversea = false, int $areaId = 0,bool $active = true)
    {
        $centers=$this->getAll()->where('oversea',$oversea)->where('active',$active);
        if($areaId) $centers=$centers->where('areaId',$areaId);

        return $centers;
    }
    public function  getLocalCenters(bool $active = true)
    {
        return $this->getAll()->where('oversea',false)->where('active',$active);
    }

    

    public function getCenterByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
         
    }

    public function getOrdered($centers)
    {
        return $centers->orderBy('importance','desc');
    }

    public function updateImportance($id,$importance)
    {
        $center=Center::find($id);
        $center->importance=$importance;
        $center->save();
    }

    
    function getMinImportance()
    {
        $min = Center::all()->min('importance');
        if(!$min) return 0;
        return $min;
    }

    public function getContactInfo(Center $center)
	{
		return $center->getContactInfo();
	}
	public function setContactInfo(Center $center, ContactInfo $contactInfo , Address $address)
	{
		$exist=$this->getContactInfo($center);
		if($exist){
			$exist->address->update($address->toArray());
			$exist->update($contactInfo->toArray());
		}else{
			DB::transaction(function() use($center,$contactInfo,$address) {
				$center->contactInfoes()->save($contactInfo);
				$contactInfo->address()->save($address);
			});
		}
	}
    
    public function importCenters($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $centerList=$excel->toArray()[0];
        for($i = 1; $i < count($centerList); ++$i) {
            $row=$centerList[$i];
            
            $name=trim($row['name']);
            if(!$name){
               continue;
            }

            $code=trim($row['code']);  
            if(!$code){
                $err_msg .= '必須填寫中心代碼';
                continue;
            }

            $exist_center=$this->getCenterByCode($code);
            if($exist_center){
                $err_msg .= '中心代碼' . $code .'重複了';
                continue;
            }
            
            $oversea=(int)trim($row['oversea']) > 0 ? true:false;
            $area_id=0;

            if($oversea){


            }else{
                $area_id=trim($row['area_id']);  
                if(!$area_id){
                    $err_msg .= '必須填寫區域代碼';
                    continue;
                }

                $area = Area::find($area_id);
                if(!$area){
                    $err_msg .= '區域代碼 ' . $area_id . ' 錯誤' ;
                    continue;
                }

            }

            $importance=(int)trim($row['importance']);

            $course_tel=trim($row['course_tel']);

            $center=new Center([
                'name' => $name,
                'code' => $code,
                'oversea' => $oversea,
                'courseTel' => $course_tel,
                'importance' => $importance,

                'active' => true,
                'updatedBy' => $updatedBy
            ]);

            if($oversea){
                $this->createCenter($center);
                continue;
            }

            $center->areaId=$area_id;
            
            $district=null;
            $zipcode=trim($row['zipcode']);
            if($zipcode) $district=District::with(['city'])->where('zipcode',$zipcode)->first();
            if(!$district){
                $err_msg .= '郵遞區號' . $zipcode . '錯誤';
                continue;
            }
          
            $street=trim($row['street']);
           
            $address=new Address([
                'districtId'=>$district->id,
                'street' => $street,
                'updatedBy' => $updatedBy
            ]);
            
            $tel=trim($row['tel']);
            $fax=trim($row['fax']);

            $contactInfo=new ContactInfo([
                'tel'=>$tel,
                'fax' => $fax,
            ]);
            
            $this->createCenter($center,$contactInfo,$address);    

        }  //end for  

        return $err_msg;
    }
    
}