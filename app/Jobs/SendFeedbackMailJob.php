<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendFeedbackMail;

class SendFeedbackMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $period;
    protected $comment;
    protected $locale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $month, $comment, $locale )
    {
        $this->url = $url;
        $this->period = $month;
        $this->comment = $comment;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        Mail::to( config('mail.recipient.address') )->locale($this->locale)->send( new SendFeedbackMail( $this->url, $this->period, $this->comment ) );
    }
}
