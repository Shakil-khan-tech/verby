<?php

namespace App\Http\Traits;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Employee;
use App\Models\Record;
use App\Classes\Helpers\Record as RecordHelper;

trait InsuranceTrait {

    // public function hours_by_day(Employee $employee, CarbonPeriod $period) {
    public function hours_by_day($matrix, Collection $plans) {
      $calendar = collect();
      $grand_total_depa = $grand_total_restant = $grand_total_time = 0;
      foreach ($matrix as $day => $arr) {
        $calendar->put( $day, 0 );
        $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
        $pausein_arr = $pauseout_arr = collect();
        foreach ($arr as $key => $records) {
          $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
          $pausein_arr = $pauseout_arr = collect();
          //check in
          if ( $records->firstWhere('action', 0) ) {
            $checkin_time = strtotime( $records->firstWhere('action', 0)->time );
          }
          //pause in
          $pausin_records = $records->where('action', 2);
          $pausein_arr = $pausin_records->pluck('time');
          if ( $pausin_records->count() > 0 ) {
            if ( $pausin_records->count() > 1 ) {
              foreach ($pausin_records as $pin_record) {
                $pausein_time += strtotime( $pin_record->time );
              }
            } else {
              $pausein_time = strtotime( $pausin_records->first()->time );
            }
          }
          //pause out
          $pausout_records = $records->where('action', 3);
          $pauseout_arr = $pausout_records->pluck('time');
          if ( $pausout_records->count() > 0 ) {
            if ( $pausout_records->count() > 1 ) {
              foreach ($pausout_records as $pout_record) {
                $pauseout_time += strtotime( $pout_record->time );
              }
            } else {
              $pauseout_time = strtotime( $pausout_records->first()->time );
            }
          }
          //check out
          if ( $records->firstWhere('action', 1) ) {
            $checkout_time = strtotime( $records->firstWhere('action', 1)->time );
          }

          //calculate
          if ( $checkin_time && $checkout_time ) {
            if ( $pausein_time && $pauseout_time ) {
              $total_seconds = $checkout_time - $checkin_time - ($pauseout_time - $pausein_time);
            } else {
              $total_seconds = $checkout_time - $checkin_time;
            }
            $total_seconds_with_nightshift = RecordHelper::nightShiftHours($checkin_time, $pausein_arr, $pauseout_arr, $checkout_time, $total_seconds);
            if ( $total_seconds_with_nightshift < 0 ) {
              $total_seconds_with_nightshift = 0;
            }
            $grand_total_time += $total_seconds_with_nightshift;
            $calendar[$day] += $total_seconds_with_nightshift;
          }
        }
      }
      $calendar->put( 'grand_total_time', $grand_total_time );

      $calendar_days = collect();
      foreach ($calendar as $day => $value) {
        // $calendar[$day] = \Carbon\CarbonInterval::seconds( $value )->cascade()->forHumans(['short' => true, 'options' => 0]);
        $calendar[$day] = number_format( (float)($value / 3600), 2, '.', '' );
        if ( $day != 'grand_total_time' ) {
          $calendar_days->put( Carbon::parse($day)->format('d'), $calendar[$day] );
        } else {
          $calendar_days->put( 'grand_total_time', $calendar[$day] );
        }
      }
      
      for ($d=1; $d <= 31; $d++) {
        if ( !$calendar_days->has( sprintf('%02d', $d) ) ) {
          // $calendar_days->put( $d, "0.00" );
        }
      }

      //Add 8.4 if employee has zero hours AND has a plan containing one of: 'F','W','K','U','UN'
      foreach ($plans as $plan) {
        $key = Carbon::parse($plan->dita)->format('d');

        /*
        OLD CODE LOGIC - NOT USED ANYMORE
        if ( $calendar_days[ $key ] == '0.00' ) {
          switch ($plan->symbol) {
            case 'K': //Krank
              $calendar_days[ $key ] = 'A';
              break;
            case 'U': //Unfall
              $calendar_days[ $key ] = 'A';
              break;
            case 'F': //Ferie
              $calendar_days[ $key ] = 'E';
              break;

            default:
              # code...
              break;
          }
          $calendar_days[ $key ] = 8.4;
        }
        */

        switch ($plan->symbol) {
          case 'K': //Krank - Sick
          case 'U': //Unfall - Accident
          case 'S': //Schwanger - Pregnant
            $calendar_days[ $key ] = 'A';
            break;
          case 'SC':
          case 'MSE':
          case 'VSE':
            $calendar_days[ $key ] = 'B';
            break;
          case 'W': //Wunsch Frei - Free Request
          case 'UN': //Unentschuldigt - Unexcused
            $calendar_days[ $key ] = 'D';
            break;
          case 'F': //Ferie - Holiday
            $calendar_days[ $key ] = 'E';
            break;
          default:
            # code...
            break;
        }
      }
      
      return $calendar_days;        
    }
}
