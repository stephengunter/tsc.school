<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;

use App\User;
use App\Account;
use App\Photo;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class MyReportsController extends Controller
{
    
     public function index()
     {
        return view('my-report');
     }

    

    
}
