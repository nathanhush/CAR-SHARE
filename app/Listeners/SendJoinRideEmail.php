<?php

namespace App\Listeners;

use App\Events\JoinRideEvent;
use App\Mail\JoinRideMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendJoinRideEmail
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
     * @param  \App\Events\JoinRideEvent  $event
     * @return void
     */
    public function handle(JoinRideEvent $event)
    {
        Mail::to($event->driver->email)->send(
            new JoinRideMail($event->passenger, $event->driver)
        );
    }
}
