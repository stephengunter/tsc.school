<?php

namespace App\Listeners;

use App\Events\StudentTrans;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Signup;
use App\SignupDetail;

class OnStudentTrans
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Handle the event.
     *
     * @param  CourseShutDown  $event
     * @return void
     */
    public function handle(StudentTrans $event)
    {
        $tran=$event->tran;

        $signupDetail=SignupDetail::with(['signup'])->find($tran->signupDetailId);

        if($signupDetail){
            
            $signupDetail->signup->update([
                'status' => -1 
            ]);
        }

    }
}
