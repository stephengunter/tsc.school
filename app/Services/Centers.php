<?php

namespace App\Services;

use App\Center;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Area;
use App\Services\Import;
use DB;
use Excel;

class Centers 
{
    use Import;

    public function __construct()
    {
        $this->with=['area','contactInfoes.address.district.city'];
    }

    public function getKeyOptions($withOversea=true)
    {
        $options = array(
            ['value'=> 'west' , 'text' => '西部'],
            ['value'=> 'east', 'text' => '東部']
           
        );

        if($withOversea) array_push($options,['value'=> 'oversea', 'text' => '海外']);
        return $options;
    }

    public function getAll()
    {
        return Center::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {
        return $this->getAll()->where('id',$id)->first();
    }

    public function getByIds(array $ids)
    {
        return $this->getAll()->whereIn('id',$ids);
    }

    public function getCentersByKey($key)
    {
        return $this->getAll()->where('key',$key);
        
    }

    public function getEastCenters()
    {
        return $this->getCentersByKey('east');
        
    }

    public function getWestCenters()
    {
        return $this->getCentersByKey('west');
    }

    public function getOverseaCenters(bool $active = true)
    {
        return $this->getAll()->where('key','oversea')->where('active',$active);
    }
    

    public function createCenter(Center $center, array $contactInfoValues=[],array $addressValues=[])
    {
        if($center->code=='A') $center->head=true;
        
        if(!$center->importance)
        {
            $min=$this->getMinImportance();

            $center->importance=$min - 1;
        }

        $center->save();

        if($contactInfoValues) $center->setContactInfo($contactInfoValues,$addressValues);
        
    }
    
    public function fetchCenters($key,bool $active = true)
    {
        
        $centers=$this->getAll()->where('key',$key)->where('active',$active);
       

        return $centers;
    }
    public function  getLocalCenters(bool $active = true)
    {
        return $this->getAll()->whereIn('key',['east','west'])->where('active',$active);
    }

    public function getCenterByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
         
    }

    public function getOrdered($centers)
    {
        return $centers->orderBy('importance','desc');
    }

    public function mapToOptions($centers,$withEmpty=false)
    {
        $options = $centers->map(function ($center) {
            return $center->toOption();
        })->all();

        if($withEmpty) array_unshift($options, ['text' => '所有中心' , 'value' =>'0']);
        
        return $options;
    }

    public function centerOptions($withEmpty=true)
    {
        $localCenters= $this->getLocalCenters(true)->get();
        return $this->mapToOptions($localCenters,$withEmpty);
    }
    public function options($withEmpty=false)
    {
        return $this->centerOptions($withEmpty);
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

            $east=(int)trim($row['east']) > 0 ? true:false;

            $importance=(int)trim($row['importance']);
            $course_tel=trim($row['course_tel']);
           

            $centerValues=[
                'name' => $name,
                'code' => $code,
                'key' => '',
                'courseTel' => $course_tel,
                'importance' => $importance,

                'active' => true,
                'updatedBy' => $updatedBy
            ];

            


            $center=new Center($centerValues);

            if($oversea){
                dd($oversea);
                $tel=trim($row['tel']);
		        $fax=trim($row['fax']);
                $street=trim($row['street']);

                $contactInfoValues=[
                    'tel'=>$tel,
                    'fax' => $fax,
                    'updatedBy' => $updatedBy
                ];

                $addressValues=[
                    'street' => $street,
                    'updatedBy' => $updatedBy
                ];

                $centerValues['key'] = 'oversea';

                $this->createCenter($center,$contactInfoValues,$addressValues);    
                continue;
            }

            

            $center->areaId=$area_id;
            if($east) $center->key = 'east';
            else $center->key = 'west';


            $contactInfoValues=null;
            $addressValues=null;

            $getContactInfo=$this->getContactInfo($row,$updatedBy);
           
            if(array_key_exists('err',$getContactInfo)){
                $err_msg .= $getContactInfo['err'] ;	
                continue;	
            }

            $contactInfoValues=$getContactInfo['contactInfoValues'];
            $addressValues=$getContactInfo['addressValues'];

            
            $this->createCenter($center,$contactInfoValues,$addressValues);    

        }  //end for  

        return $err_msg;
    }
    
}