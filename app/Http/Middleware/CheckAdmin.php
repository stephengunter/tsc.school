<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckAdmin
{
    public static function canLogin($user)
    {
        
        if(!$user) return false;

        if($user->isDev()) return true;
        if($user->isBoss()) return true;
        if($user->isStaff()) return true;

        return false;
    }
    

    public function handle($request, Closure $next)
    {
        $user=request()->user();

        $can_login= static::canLogin($user);

        if($can_login) return $next($request);
        throw new AuthenticationException();
        
    }

    public static function exceptions($user=null)
    {
        
        if($user) auth()->logout();

        throw new AuthenticationException();
    }
    

    
}
