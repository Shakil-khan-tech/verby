<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\IssueListingTechnicianMail;

class IssueListingTechnicianMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $listing;
    protected $to;
    protected $locale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $listing, $to, $locale )
    {
        $this->url = $url;
        $this->listing = $listing;
        $this->to = $to;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to( $this->to )->locale($this->locale)->send( new IssueListingTechnicianMail( $this->url, $this->listing ) );
    }
}
