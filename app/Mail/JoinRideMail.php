<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JoinRideMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */



    public $passenger;

    public $driver;

    public function __construct($passenger, $driver)
    {
        $this->passenger = $passenger;
        $this->driver = $driver;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.joinride',[
            'passengerfirstname' => $this->passenger->firstname,
            'passengerlastname' => $this->passenger->lastname,
            'passengeremail' => $this->passenger->email]);
    }
}
