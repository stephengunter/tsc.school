<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function currentUser()
    {        
        if(!request()->ajax()){
            return view('app');
        }
        return auth()->user();
    }

    protected function currentUserId()
    {
        return $this->currentUser()->id;
    }

    protected function isAjaxRequest()
    {
        return request()->ajax();
    }
}
