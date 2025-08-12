<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueListingMail extends Mailable
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
        if ( $this->listing->done == 0 ) {
            return $this->markdown('emails.issues.request_listing')
                        ->subject('Issue requested')
                        ->with([
                            'url' => $this->url,
                            'listing' => $this->listing,
                        ]);
        } else {
            return $this->markdown('emails.issues.fix_listing')
                        ->subject('Issue fixed')
                        ->with([
                            'url' => $this->url,
                            'listing' => $this->listing,
                        ]);
        }
        
    }
}
