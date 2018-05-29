<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    
    public function __construct(User $user,$token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
        $subject=config('app.company.fullname') . ' - ' .'重設密碼認證信';
        $username=$this->user->name;
        if($this->user->profile->fullname)$username=$this->user->profile->fullname;

        return $this->markdown('emails.resetpassword')->with([
                            'username' => $username,
                            'url' => url('password/reset',  $this->token),
                        ])->subject($subject);

       
    }
}
