<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Menus;
use App\Role;
use Route;
use App\Center;

use Illuminate\Auth\AuthenticationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected function unauthorized()
    {
         throw new AuthenticationException();
    }
    protected function requestError($errors)
    {
       
        return response() ->json(['errors'=> $errors ],422);
       
    }
    

    protected function currentUser()
    {     
        return auth()->user();
    }

    protected function currentAdmin()
    {
        return $this->currentUser()->admin;
    }

    protected function currentUserId()
    {
        return $this->currentUser()->id;
    }

    protected function currentUserIsDev()
    {
        $roleName=Role::devRoleName();
        return $this->currentUser()->hasRole($roleName);
    }

    

    protected function centersCanAdmin()
    {
        if($this->currentUserIsDev()) return Center::where('removed',false)->get();
        $admin = $this->currentAdmin();
        if(!$admin) return [];

        return $admin->centersCanAdmin();
    }

    protected function canAdminCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;

        return $this->canAdminCenters([$center]);
    }

    protected function canAdminCenters($centers)
    {
        if($this->currentUserIsDev()) return true;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect($centers);

        if(count($intersect)) return true;
        return false;
    }

    protected function isAjaxRequest()
    {
        return request()->ajax();
    }

    protected function  adminMenus($key)
    {
        $current=Route::current()->uri();
        return Menus::adminMenus($current,$key);

    }

    protected function  clientMenus()
    {
        $current=Route::current()->uri();
        $user=$this->currentUser();
      
        if($this->currentUser()){
            return [
                'current' => $current,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->profile->fullname
                ],
                'teacher' => $user->isTeacher(),
                'students' => $user->isStudent()
            ];
        }else{
            return [
                'current' => $current,
                'user' => null,
                'teacher' => false,
                'students' => false
            ];
        }
      

    }
}
