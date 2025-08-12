<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupplyListingMail;

class SupplyListingMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $listing;
    protected $cc;
    protected $locale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $listing, $cc = null, $locale )
    {
        $this->url = $url;
        $this->listing = $listing;
        $this->cc = $cc;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->cc ) {
            Mail::to( config('mail.recipient.address') )->cc( $this->cc )->locale($this->locale)->send( new SupplyListingMail( $this->url, $this->listing ) );
        } else {
            Mail::to( config('mail.recipient.address') )->locale($this->locale)->send( new SupplyListingMail( $this->url, $this->listing ) );
        }
    }
}
