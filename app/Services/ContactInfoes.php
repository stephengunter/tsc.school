<?php

namespace App\Services;

use App\ContactInfo;
use App\Address;
use DB;

class ContactInfoes 
{
    public function __construct()
    {
        $this->with=['address.district.city'];
    }

    
    public function getById($id)
    {
        return ContactInfo::with($this->with)->find($id);
    }

    public function createContactInfo(ContactInfo $contactInfo,Address $address)
    {
        $contactInfo=DB::transaction(function() use($contactInfo,$address) {
            $contactInfo->save();
            $contactInfo->address()->save($address);
            return $contactInfo;
        });

        return $contactInfo;
    }
    
    

   
    
    
    
}