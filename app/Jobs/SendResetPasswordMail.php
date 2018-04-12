<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Mail\ResetPassword;
use Mail;
use App\User;

class SendResetPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $token;
    
    public function __construct(User $user,$token)
    {
       $this->user = $user;
       $this->token = $token;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user=$this->user;
        $token=$this->token;

        $email=filter_var($user->email, FILTER_VALIDATE_EMAIL);
        
      
        if($email){
            Mail::to($user)->send(new ResetPassword($user,$token));
        }
    }
}
