<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeRecords;

class EmployeeRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $month;
    protected $to;
    protected $expiration;
    protected $employee;
    protected $locale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $month, $to, $expiration, $employee, $locale )
    {
        $this->url = $url;
        $this->month = $month;
        $this->to = $to;
        $this->expiration = $expiration;
        $this->employee = $employee;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to( $this->to )->locale($this->locale)->send( new EmployeeRecords( $this->url, $this->month, $this->expiration, $this->employee ) );
    }
}
