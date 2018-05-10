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

class AccountsController extends Controller
{
    
    function canEdit(User $user)
    {
        if($this->currentUserIsDev()) return true;
        if($user->teacher){
            if(!count($user->teacher->centers)) return true;

            return $this->canAdminCenters($user->teacher->centers);
        }
        return true;
    }

    public function create()
    {
        $request=request();

        $userId=0;
        if($request->user)  $userId=(int)$request->user;

        $account = Account::init();
        $account['userId']=$userId;
      
        $form=[
            'account' => $account
        ];

        return response() ->json($form);
        
    }
    public function store(AccountRequest $request)
    {
        $userId=$request->getUserId();
        $user=User::findOrFail($userId);

        if(!$this->canEdit($user)) return $this->unauthorized();

        $user->setAccount(new Account($request->getValues()));
        
        return response() ->json();
        
    }

    public function edit($id)
    {
        
        $account = Account::findOrFail($id);
      
        $form=[
            'account' => $account
        ];

        return response() ->json($form);
        
    }


    public function update(AccountRequest $request, $id)
    {
        $account = Account::findOrFail($id);
        
        if(!$this->canEdit($account->user)) return $this->unauthorized();
        
        $account->update($request->getValues());

        return response() ->json();
    }

    public function destroy($id) 
    {
        $account = Account::findOrFail($id);
        if(!$this->canEdit($account->user)) return $this->unauthorized();
        
        if($account->getPhoto()){
            $photo=$account->getPhoto();
            $photo->delete();
        }

        $account->delete();
        return response() ->json();
    }

    

    
}
