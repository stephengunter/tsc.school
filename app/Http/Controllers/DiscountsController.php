<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DiscountRequest;

use App\Discount;
use App\ContactInfo;
use App\Address;
use App\Area;
use App\City;
use App\Services\Centers;
use App\Services\Discounts;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class DiscountsController extends Controller
{
   
    
    public function __construct(Discounts $discounts,Centers $centers)
    {
        $this->discounts=$discounts;
        $this->centers=$centers;
    }

    function keyOptions()
    {
        $withOversea=false;
        return $this->centers->getKeyOptions($withOversea);

    }

    function canEdit()
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;
        return $this->currentUser->admin->isHeadDiscountAdmin();
    }
    function canDelete()
    {
        return $this->canEdit();
    }

    public function index()
    {
       
        $request=request();
        
        $key='';
        if($request->key)  $key = $request->key;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);

        $keys=['west','east'];
        if(!in_array($key,$keys)) $key=$keys[0];

        $discounts=$this->discounts->fetchDiscounts($key,$active);
       
        $discounts=$this->discounts->getOrdered($discounts);
       
      
        $pageList = new PagedList($discounts);

        foreach($pageList->viewList as $discount){
            $discount->loadViewModel();
        } 

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');
       
        return view('discounts.index')->with([
            'title' => '折扣管理',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'key' => $key,
            'keys' => $this->keyOptions(),
            'list' =>  $pageList
        ]);
    }


    public function update(DiscountRequest $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();
       
        $values=$request->getValues();
        $values['updatedBy'] = $current_user->id;
        
        $discount->update($values);
        return response() ->json();
    }

    public function destroy($id) 
    {
        $discount = Discount::findOrFail($id);
        if(!$this->canDelete()) return $this->unauthorized();

        $discount->updatedBy=$this->currentUserId();
        $discount->removed=true;
        $discount->save();
       
        return response() ->json();
    }

    
}
