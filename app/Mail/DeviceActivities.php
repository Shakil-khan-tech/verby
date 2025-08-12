<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeviceActivities extends Mailable
{
    use Queueable, SerializesModels;

    protected $devices;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($devices)
    {
        $this->devices = $devices;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->markdown('emails.devices.activities')
                    ->subject('Device activities')
                    ->with([
                        'devices' => $this->devices,
                    ]);
    }
}
