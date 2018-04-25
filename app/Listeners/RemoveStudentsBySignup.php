<?php

namespace App\Listeners;

use App\Events\SignupUnPayed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Students;

class RemoveStudentsBySignup
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
     * @param  SignupUnPayed  $event
     * @return void
     */
    public function handle(SignupUnPayed $event)
    {
        $signup=$event->signup;
      
        foreach($signup->details as $detail){
            $this->students->removeStudent($detail->courseId, $signup->userId);
        }
    }
}
