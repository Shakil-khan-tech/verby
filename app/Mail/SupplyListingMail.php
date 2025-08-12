<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplyListingMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $listing;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $supply_listing)
    {
        $this->url = $url;
        $this->listing = $supply_listing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ( $this->listing->done == 0 ) {
            return $this->markdown('emails.supplies.request_listing')
                        ->subject('Inventory requested')
                        ->with([
                            'url' => $this->url,
                            'listing' => $this->listing,
                        ]);
        } else {
            return $this->markdown('emails.supplies.fix_listing')
                        ->subject('Inventory fulfilled')
                        ->with([
                            'url' => $this->url,
                            'listing' => $this->listing,
                        ]);
        }
        
    }
}
