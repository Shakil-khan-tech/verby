<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueListingTechnicianMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $listing;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $issue_listing)
    {
        $this->url = $url;
        $this->listing = $issue_listing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.issues.listing_external')
                    ->subject('Issue requested')
                    ->with([
                        'url' => $this->url,
                        'listing' => $this->listing,
                    ]);
        
    }
}
