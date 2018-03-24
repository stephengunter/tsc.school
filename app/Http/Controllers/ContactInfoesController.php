<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactInfoRequest;

use App\ContactInfo;
use App\Address;
use App\Area;
use App\City;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

use App\Services\ContactInfoes;

class ContactInfoesController extends Controller
{
    public function __construct(ContactInfoes $contactInfoes)
    {
        $this->contactInfoes=$contactInfoes;
     
    }
    function cityOptions()
    {
        $cities=City::with(['districts'])->get(); 
        return $cities;
    }
   

    public function create()
    {
        $cityOptions=$this->cityOptions();
        $contactInfo=ContactInfo::init();

        $contactInfo['address']['cityId']=$cityOptions->first()->id;
      
        $form=[
            'contactInfo' => $contactInfo,
            'cityOptions' => $cityOptions
        ];

        return response() ->json($form);
      
    }

    public function store(ContactInfoRequest $request)
    {
        $current_user=$this->currentUser();
       
        $contactInfoValues=$request->getContactInfoValues();
      
        $addressValues=$request->getAddressValues();

        $contactInfoValues['updatedBy'] = $current_user->id;
        $addressValues['updatedBy'] = $current_user->id;
      
        $contactInfo=new ContactInfo($contactInfoValues);
        $address=new Address($addressValues);

        $contactInfo = $this->contactInfoes->createContactInfo($contactInfo,$address);
        return response() ->json($contactInfo);
    }

    public function show($id)
    {
        $contactInfo = $this->contactInfoes->getById($id);
        if(!$contactInfo) abort(404);

        $contactInfo->address->fullText();

        return response() ->json($contactInfo);
        
    }

    public function edit($id)
    {
        $contactInfo = $this->contactInfoes->getById($id);
        if(!$contactInfo) abort(404);

        $contactInfo->address->cityId=$contactInfo->address->district->city->id;
        
        $cityOptions=$this->cityOptions();
      
        $form=[
            'contactInfo' => $contactInfo,
            'cityOptions' => $cityOptions
        ];

        return response() ->json($form);
        
    }


    public function update(ContactInfoRequest $request, $id)
    {
        $contactInfo = $this->contactInfoes->getById($id);
        if(!$contactInfo) abort(404);

        $current_user=$this->currentUser();

       
        $contactInfoValues=$request->getContactInfoValues();
        $addressValues=$request->getAddressValues();

        $contactInfoValues['updatedBy'] = $current_user->id;
        $addressValues['updatedBy'] = $current_user->id;
        
        $contactInfo->update($contactInfoValues);
        $contactInfo->address->update($addressValues);

        return response() ->json();
    }

    public function destroy($id) 
    {
        $contactInfo = ContactInfo::findOrFail($id);
        $contactInfo->delete();
       
        return response() ->json();
    }
}
