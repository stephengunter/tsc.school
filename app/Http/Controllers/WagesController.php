<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WageRequest;

use App\Wage;
use App\Services\Wages;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class WagesController extends Controller
{
   
    
    public function __construct(Wages $wages)
    {
        $this->wages=$wages;
    }

    function canEdit()
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;
        return $this->currentUser->admin->isHeadWageAdmin();
    }
    function canDelete()
    {
        return $this->canEdit();
    }

    function getPS()
    {
        $night=config('app.wage.night.east');
        $night=Helper::toTimeString($night);
        $bigEast=config('app.wage.big.east');
        $bigWest=config('app.wage.big.west');

        return [
            'night' => sprintf('開始上課時間在 %s 以後', $night),
            'bigWest' => sprintf('學員人數大於等於 %d 人', $bigWest),
            'bigEast' => sprintf('學員人數大於等於 %d 人', $bigEast),
        ];
        

    }

    public function index()
    {
        $wages=$this->wages->getAll();
      
        $pageList = new PagedList($wages);
      

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }

        $ps=$this->getPS();
       

        $menus=$this->adminMenus('TeachersAdmin');
       
        return view('wages.index')->with([
            'title' => '薪酬標準',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'ps' => $ps,
            'list' =>  $pageList
        ]);
    }


    public function update(WageRequest $request, $id)
    {
        $wage = Wage::findOrFail($id);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();
       
        $values=$request->getValues();
        $values['updatedBy'] = $current_user->id;
        
        $wage->update($values);
        return response() ->json();
    }

    

    
}
