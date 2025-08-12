<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeInsurance extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdf;
    protected $period;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, $month)
    {
        $this->pdf = $pdf;
        $this->period = $month;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->markdown('emails.employees.insurance_report')
                    ->subject('Insurance Report for ' . $this->period)
                    ->with([
                        'period' => $this->period,
                    ])
                    ->attach($this->pdf, [
                        'as' => $this->period . '-insurance.pdf',
                        'mime' => 'application/pdf',
                   ]);
    }
}
