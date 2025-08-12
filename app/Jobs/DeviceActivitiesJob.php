<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Device;
use Carbon\Carbon;
use App\Mail\DeviceActivities;
use Illuminate\Support\Facades\Mail;

class DeviceActivitiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $last_activities = Device::select('devices.id')
        ->selectRaw('MAX(records.updated_at) updated_at, devices.name')
        ->leftJoin('records','records.device_id','=','devices.id')
        ->groupBy('id')
        ->get();

        $devices = collect();

        foreach ($last_activities as $key => $activity) {
            if ( $activity->updated_at < Carbon::now()->subDay() ) {
            $devices->push( [
                'name' => $activity->name,
                'updated_at' => $activity->updated_at,
            ] );
            }
        }

        Mail::to( config('mail.recipient.address') )->send( new DeviceActivities( $devices ) );
    }
}
