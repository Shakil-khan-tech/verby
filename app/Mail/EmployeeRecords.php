<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRecords extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $period;
    protected $expiration;
    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $month, $expiration, $employee)
    {
        $this->url = $url;
        $this->period = $month;
        $this->expiration = $expiration;
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->markdown('emails.employees.records_report')
                    ->subject('Timesheet Report for ' . $this->period)
                    ->with([
                        'url' => $this->url,
                        'period' => $this->period,
                        'expiration' => $this->expiration,
                        'employee' => $this->employee,
                    ]);
    }
}
