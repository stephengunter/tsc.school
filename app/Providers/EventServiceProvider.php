<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CourseShutDown' => [
            'App\Listeners\OnCourseShutDown',  //產生全額退費
        ],
        'App\Events\SignupPayed' => [
            'App\Listeners\OnSignupPayed',  
        ],
        'App\Events\SignupUnPayed' => [
            'App\Listeners\RemoveStudentsBySignup',  //刪除學員
        ],
        'App\Events\StudentTrans' => [
            'App\Listeners\OnStudentTrans',  //學員轉班, 將報名狀態設為"已取消"
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
