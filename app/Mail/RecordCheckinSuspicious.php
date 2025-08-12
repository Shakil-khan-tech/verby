<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordCheckinSuspicious extends Mailable
{
    use Queueable, SerializesModels;

    protected $employee;
    protected $checkin;
    protected $checkout;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee, $checkin, $checkout)
    {
        $this->employee = $employee;
        $this->checkin = $checkin;
        $this->checkout = $checkout;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->markdown('emails.records.suspicious')
                    ->subject('Suspicious checkin for ' . $this->employee->name)
                    ->with([
                        'employee' => $this->employee,
                        'checkin' => $this->checkin,
                        'checkout' => $this->checkout,
                    ]);
    }
}
