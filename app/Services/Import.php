<?php

namespace App\Services;

use App\User;
use App\Profile;
use App\Role;
use App\Identity;
use App\Center;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Core\Helper;
use Exception;

trait Import
{
    public function getImportUserDatas($row,$updatedBy)
	{
		$err_msg='';

		$phone=trim($row['phone']);
		$email=trim($row['email']);
		

		$userValues=[
			'email' => $email,
			'phone' => $phone,
			'updatedBy' => $updatedBy
		];

		

		$sid=strtoupper(trim($row['id']));
		$fullname=trim($row['fullname']);
		$gender=trim($row['gender']);

		
		
		if(Helper::isSIDPattern($sid)){
			if(Helper::isTaiwanSID($sid)){
				//中華民國身分證
				if(!Helper::checkSID($sid)){
					$err_msg .=  $fullname . '身分證錯誤';			
				}else $gender=Helper::getGenderFromSID($sid);
				
			}else{
				
				$gender = trim($row['gender']);
			    $gender = Helper::isTrue((int)$gender);
		
			}
		
		}else{
			$err_msg .=  $fullname . '身分證錯誤';	
		}
		

		
		$dob=trim($row['dob']);
		
		if($dob){
		
			try {  
				$pieces=explode('/', $dob);
				$year = (int)$pieces[0] + 1911;
				$dob= $year . '/'.$pieces[1]. '/'.$pieces[2];   
		
			}catch (Exception $e) {  
				$err_msg .=  $fullname . '生日錯誤';			
			}  
			             
		}

		
		
		
		

		
		$profileValues=[
			'fullname' => $fullname,
			'sid' => $sid,
			'gender' => $gender,
			'dob' =>$dob,
		   
			'updatedBy' => $updatedBy
		]; 

		$contactInfoValues=null;
		$addressValues=null;

		$getContactInfo=$this->getContactInfo($row,$updatedBy);
		if(array_key_exists('err',$getContactInfo)){
			$err_msg .= $fullname . $getContactInfo['err'] . ',';		
		}else{
			$contactInfoValues=$getContactInfo['contactInfoValues'];
			$addressValues=$getContactInfo['addressValues'];
		}

		
		

		$identityNames=explode(',', trim($row['identities']));
        $identities=[];
        foreach($identityNames as $identityName){
            $identity=Identity::where('name',$identityName)->first();
            if($identity){
				array_push($identities, $identity);
            } 
          
		}

		if($err_msg){
			return [
				'err' => $err_msg
			];
		}

		return [
			'userValues' => $userValues,
			'profileValues' => $profileValues,
			'contactInfoValues' => $contactInfoValues,
			'addressValues' => $addressValues,
			'identities' => $identities
		];

	}
	
	public function getContactInfo($row,$updatedBy)
	{
		$tel='';
		if(array_key_exists ( 'tel' ,$row)){
			$tel=trim($row['tel']);
		}  
		
		$fax='';
		if(array_key_exists ( 'fax' ,$row)){
			$tel=trim($row['fax']);
		}  



		$street=trim($row['street']);
		if(!$street){
			$district=District::where('zipcode','100')->first();
			$addressValues=[
				'districtId'=>$district->id,
				'street' => $street,
				'updatedBy' => $updatedBy
			];
			
			$contactInfoValues=[
				'tel'=>$tel,
				'fax' => $fax,
				'updatedBy' => $updatedBy
			];
	
			return [
				'addressValues'=>$addressValues,
				'contactInfoValues'=>$contactInfoValues,
			];
		}


		
		



		$district=null;
		$zipcode=trim($row['zipcode']);

		if($zipcode) $district=District::with(['city'])->where('zipcode',$zipcode)->first();
		if(!$district){
			
			return [
				'err' => '郵遞區號' . $zipcode . '錯誤'
			];		
		}
		
		$addressValues=[
			'districtId'=>$district->id,
			'street' => $street,
			'updatedBy' => $updatedBy
		];
		
		$contactInfoValues=[
			'tel'=>$tel,
			'fax' => $fax,
			'updatedBy' => $updatedBy
		];

		return [
			'addressValues'=>$addressValues,
			'contactInfoValues'=>$contactInfoValues,
		];

	}
    
    public function getCenters($row)
    {
        $err_msg='';
        $center_codes=explode(',', trim($row['centers']));
        $centers=[];
        foreach($center_codes as $code){
            $center=Center::where('code',$code)->first();
            if(!$center){
                $err_msg .= '中心代碼' . $code . '錯誤';
                continue;     
            } 
            array_push($centers, $center);
        }

        if($err_msg){
			return [
				'err' => $err_msg
			];
        }
        
        return [
			'centers' => $centers
		];
    }

}