<?php

namespace App\Listeners;

use App\Events\CourseShutDown;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Quits;

class CreateQuitsByCourse
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Quits $quits)
    {
        $this->quits=$quits;
    }

    /**
     * Handle the event.
     *
     * @param  CourseShutDown  $event
     * @return void
     */
    public function handle(CourseShutDown $event)
    {
        $this->quits->createQuitsByCourse($event->course, $event->percent);
    }
}
