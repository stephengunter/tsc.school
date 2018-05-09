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

		if(!Helper::checkSID($sid)){
			$err_msg .=  $fullname . '身分證錯誤';			
		}

		$gender=Helper::getGenderFromSID($sid);

		$dob=trim($row['dob']);
		if($dob){
			$pieces=explode('/', $dob);
			$year = (int)$pieces[0] + 1911;
			$dob= $year . '/'.$pieces[1]. '/'.$pieces[2];                
		}

		
		$profileValues=[
			'fullname' => $fullname,
			'sid' => $sid,
			'gender' => $gender,
			'dob' => $dob,
		   
			'updatedBy' => $updatedBy
		]; 

		
		$street=trim($row['street']);
		$district=null;
		$zipcode=trim($row['zipcode']);

		if($zipcode) $district=District::with(['city'])->where('zipcode',$zipcode)->first();
		if(!$district){
			$err_msg .= '郵遞區號' . $zipcode . '錯誤';			
		}
		
		$addressValues=[
			'districtId'=>$district->id,
			'street' => $street,
			'updatedBy' => $updatedBy
		];
		
		$contactInfoValues=[
			'tel'=>'',
			'fax' => '',
		];

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