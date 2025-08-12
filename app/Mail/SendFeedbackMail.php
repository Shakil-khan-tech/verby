<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $period;
    protected $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $url, $period, $comment )
    {
        $this->url = $url;
        $this->period = $period;
        $this->comment = $comment;
    }

    public function build()
    {
        // return $this->view('view.name');
        return $this->markdown('emails.employees.feedback')
                    ->subject('User Timesheet Feedback')
                    ->with([
                        'url' => $this->url,
                        'period' => $this->period,
                        'comment' => $this->comment,
                    ]);
    }
}
