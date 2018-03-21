<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Terms;

class TermsController extends Controller
{
    
    public function __construct(Terms $terms)
    {
        $this->terms=$terms;
    }

    function canEdit()
    {
        return true;
    }

    
    public function index(bool $active = true)
    {
        $terms=$this->terms->fetchTerms($active);
        $terms=$this->terms->getOrdered($terms);

        

        return view('terms.index');
    }
}
