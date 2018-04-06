<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Center;
use App\ContactInfo;
use App\Address;
use App\Area;
use App\City;
use App\Services\Centers;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CentersController extends Controller
{
    
    public function __construct(Centers $centers)
    {
        $this->centers=$centers;
     
    }
    
    public function index()
    {
        $localCenters = $this->centers->getLocalCenters()->get();
       
        $overseaCenters = $this->centers->getOverseaCenters()->get();
        foreach($overseaCenters as $center){
            $center->loadContactInfo();
        } 

        $areaIds=array_unique($localCenters->pluck('areaId')->toArray());
        
        $areas=Area::whereIn('id',$areaIds)->get();
        foreach($areas as $area){
            $centers=$localCenters->where('areaId',$area->id);
            foreach($centers as $center){
                $center->loadContactInfo();
            } 
            $area->centers=$centers;
        }



            
        $model=[
            'title' => '開課中心',
            'topMenus' => $this->clientMenus(),
          
            'areas' => $areas,
            'overseas' => $overseaCenters
        ];

        return view('client.centers')->with($model);
    }

   
}
