<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckAdmin
{
    public static function canLogin($user)
    {
        return true;
        //if(!$user) return false;
        

        // if($user->isDev()) return true;
        // if($user->isAdmin()) return true;

        //return false;
    }
    

    public function handle($request, Closure $next)
    {
        $user=request()->user();

        $can_login= static::canLogin($user);

        if($can_login) return $next($request);
        return static::exceptions($user);
        
        
        
    }
    public static function exceptions($user=null)
    {
        $email='';
        if($user){
            if(!$user->email_confirmed) $email=$user->email;
            auth()->logout();
        } 

        if($email)  throw new EmailUnconfirmed($email);

        throw new AuthenticationException();
    }

    
}
