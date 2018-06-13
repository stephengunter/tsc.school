<?php

namespace App\Services;

use App\Doc;
use DB;
use Excel;

class Docs 
{
   
    public function createDoc(Doc $doc, array $contactInfoValues=[],array $addressValues=[])
    {
        if($doc->code=='A') $doc->head=true;
        
        if(!$doc->importance)
        {
            $min=$this->getMinImportance();

            $doc->importance=$min - 1;
        }

        $doc->save();

        if($contactInfoValues) $doc->setContactInfo($contactInfoValues,$addressValues);
        
    }

    public function getAll()
    {
        return Doc::orderBy('importance','desc')->orderBy('created_at','desc');
    }

    public function getOrdered($docs)
    {
        return $docs->orderBy('importance','desc')->orderBy('created_at','desc');
    }

    
    
}