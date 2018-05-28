<?php

namespace App\Listeners;

use App\Events\SignupPayed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Students;
use Carbon\Carbon;

class OnSignupPayed
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Students $students)
    {
        $this->students=$students;
    }

    /**
     * Handle the event.
     *
     * @param  SignupPayed  $event
     * @return void
     */
    public function handle(SignupPayed $event)
    {
        
        $signup=$event->signup;

        $joinDate=$signup->getPayDate();
      
        foreach($signup->details as $detail){
            if(!$detail->canceled){
                $this->students->createStudent($detail->courseId, $signup->userId, $joinDate);
            }
            
        }

        
        if($signup->identity_ids){
            
            $identity_ids=explode(',', $signup->identity_ids);
            foreach($identity_ids as $identity_id){
                $signup->user->addIdentity($identity_id);
            }
          
        }
        
    }
}
