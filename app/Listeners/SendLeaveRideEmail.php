<?php

namespace App\Listeners;

use App\Events\LeaveRideEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\LeaveRideMail;
use Illuminate\Support\Facades\Mail;

class SendLeaveRideEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LeaveRideEvent  $event
     * @return void
     */
    public function handle(LeaveRideEvent $event)
    {
        Mail::to($event->driver->email)->send(
            new LeaveRideMail($event->passenger, $event->driver)
        );
    }
}
