<?php

namespace App\Http\Traits;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Record;

trait EmployeeTrait {

    // public function calculate_hours(String $date, Int $hall = null, Int $schedule = null) {
    public function calculate_hours(Employee $employee, String $from, String $to) {
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        $records = Record::where('employee_id', $employee->id)
        ->whereBetween('time', [$from, $to])
        // ->where('action', 0)->orWhere('action', 1)
        ->orderBy('time', 'ASC')
        ->get();

        $grand_total_time = $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
        foreach ($records as $record) {
          switch ( $record->action ) {
            case 0:
              $checkin_time = strtotime( $record->time );
              break;
            case 1:
              $checkout_time = strtotime( $record->time );
              break;
            case 2:
              $pausein_time = strtotime( $record->time );
              break;
            case 3:
              $pauseout_time = strtotime( $record->time );
              break;
            default:
              // code...
              break;
          }

          if ( $checkin_time && $checkout_time ) {
            if ( $pausein_time && $pauseout_time ) {
              $total_seconds = $checkout_time - $checkin_time - ($pauseout_time - $pausein_time);
            } else {
              $total_seconds = $checkout_time - $checkin_time;
            }
          }

          $grand_total_time += $total_seconds;
        }

        $grand_total_time = $grand_total_time / 3600;
        return $grand_total_time;
        
    }
}
