<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\IdentityRequest;

use App\Identity;
use App\Services\Identities;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class IdentitiesController extends Controller
{
   
    
    public function __construct(Identities $identities)
    {
        $this->identities=$identities;
    }
    
    function canEdit()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->isBoss();
    }
    function canDelete()
    {
        return $this->canEdit();
    }

    public function index()
    {
       
        $identities=$this->identities->getAll()->orderBy('member','desc');
      
        $pageList = new PagedList($identities);
      

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }

        $menus=$this->adminMenus('UsersAdmin');
       
        return view('identities.index')->with([
            'title' => '身分管理',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'list' =>  $pageList
        ]);
    }

    public function store(IdentityRequest $request)
    {

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();
       
        $values=$request->getValues();
        $values['updatedBy'] = $current_user->id;
        Identity::create($values);
        
        return response() ->json();
    }

    public function update(IdentityRequest $request, $id)
    {
        $identity = Identity::findOrFail($id);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();
       
        $values=$request->getValues();
        $values['updatedBy'] = $current_user->id;
        
        $identity->update($values);
        return response() ->json();
    }

    public function destroy($id) 
    {
        $identity = Identity::findOrFail($id);
        if(!$this->canDelete()) return $this->unauthorized();

        $identity->updatedBy=$this->currentUserId();
        $identity->removed=true;
        $identity->save();
       
        return response() ->json();
    }

    
}
