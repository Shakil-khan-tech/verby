<?php
namespace App\Classes\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
// use Debugbar;

class Record
{
    public static function hasNightShift($checkin, $checkout)
    {

        $night_start = Carbon::createFromTimestamp($checkin)->startOfDay()->addHours(23);
        $night_end = $night_start->copy()->addHours(7);
        $nightshift_period = new CarbonPeriod($night_start, $night_end);
        
        $checkin = Carbon::createFromTimestamp($checkin);
        $checkout = Carbon::createFromTimestamp($checkout);
        $current_period = new CarbonPeriod($checkin, $checkout);

        if (!$nightshift_period->overlaps($current_period)) {
            return false;
        }
        return true;
    }

    public static function nightShiftHours($checkin, $pauseins, $pauseouts, $checkout, $total = null)
    {
        $night_start = Carbon::createFromTimestamp($checkin)->startOfDay()->addHours(23);
        $night_end = $night_start->copy()->addHours(7);
        $nightshift_period = new CarbonPeriod($night_start, $night_end);
        
        $checkin = Carbon::createFromTimestamp($checkin);
        $checkout = Carbon::createFromTimestamp($checkout);
        $current_period = new CarbonPeriod($checkin, $checkout);

        if (!$nightshift_period->overlaps($current_period)) {
            return $total;
        }

        //calculate pauses in Night Shift
        $pauses = 0;
        for ($i=0; $i < min( count($pauseins), count($pauseouts) ); $i++) { 
            $pausein = $pauseins[$i];
            $pauseout = $pauseouts[$i];
            $pause_period = new CarbonPeriod($pausein, $pauseout);
            if (!$nightshift_period->overlaps($pause_period)) { continue; }
            $nightFirstEndDate = min($nightshift_period->calculateEnd(), $pause_period->calculateEnd());
            $nightLatestStartDate = max($nightshift_period->getStartDate(), $pause_period->getStartDate());
            $pauses += $nightFirstEndDate->diffInSeconds($nightLatestStartDate);
        }
    
        $firstEndDate = min($nightshift_period->calculateEnd(), $current_period->calculateEnd());
        $latestStartDate = max($nightshift_period->getStartDate(), $current_period->getStartDate());

        $night_work_time = $firstEndDate->diffInSeconds($latestStartDate) - $pauses;
        $night_work_time *= 0.1; //10%

        return $total + $night_work_time;
    }
}
