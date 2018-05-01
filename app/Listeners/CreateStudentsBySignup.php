<?php

namespace App\Listeners;

use App\Events\SignupPayed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Students;

class CreateStudentsBySignup
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

       
      
        foreach($signup->details as $detail){
            
            $this->students->createStudent($detail->courseId, $signup->userId);
        }
        
    }
}
