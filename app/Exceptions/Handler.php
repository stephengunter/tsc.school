<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;
use App\Exceptions\RequestError;
use App\Core\Helper;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
      
        if ($request->expectsJson()) {
             return response()->json(['error' => '權限不足','code' => 401 ], 401);
        }

        if(Helper::str_starts_with($request->path(),'manage')){
            return redirect()->guest(route('manage-login'));
        }

        
    }
    
    protected function emailUnconfirmed($request, EmailUnconfirmed $e)
    {
       
        if ($request->expectsJson()) {
            return response()->json(['error' => 'email unconfirmed','code' => 439 ], 439);
           
        }
        return redirect()->route('email-unconfirmed', ['email' => $e->email]);
        
    }
    protected function requestError($request, RequestError $e)
    {
        $err=$e->getError();
        $key=$err['key'];
        $msg=$err['value'];
        return response()->json([ $key =>  [$msg] ]  ,  422);
        
    }
}
