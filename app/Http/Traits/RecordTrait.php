<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use App\Models\Employee;
use App\Classes\Helpers\Record as RecordHelper;
// use Debugbar;

trait RecordTrait
{

  public function daily_employees_matrix(Collection $employees_func, Carbon $intended_date)
  {

    $daily_employees = collect();
    foreach ($employees_func as $key_func => $employees) {
      foreach ($employees as $key_emp => $employee) {

        $daily_employees->push($employee);

        if ($employee->records->isEmpty()) {
          continue;
        }

        $sorted_records = $employee->records->sortBy('time');

        $employee->matrix = $this->generate_matrix($sorted_records, $intended_date);
      }
    }
    return $daily_employees;
  }

  // generate matrix for multiple employees aggregated for a period of time
  public function monthly_records_matrix(
    CarbonPeriod $period,
    Collection $records,
    Collection $reports,
    Collection $employees
  ) {

    $matrix = new Collection();
    $matrix->put('days', collect());
    $matrix->put('grandtotal', collect());
    $matrix->put('grandtotal_performs', collect());
    $matrix->put('grandtotal_performs_hours', collect());
    // $matrix['grandtotal']->put("total", 0);
    foreach (config('constants.room_categories') as $key => $category) {
      $matrix['grandtotal']->put($category, 0);
    }
    foreach (config('constants.performs') as $key => $perform) {
      $matrix['grandtotal_performs']->put($perform, 0);
      $matrix['grandtotal_performs_hours']->put($perform, 0);
    }

    //new *******************************
    //  foreach ($employees as $key => $employee) {
    //   foreach ($period as $key => $day_obj) {
    //     //maybe use the loop for calculation, ma pak loopsa V
    //     $employee_matrix = $this->month_employee_matrix($period, $employee->records);
    //     $seconds += $this->calculate_hours($employee_matrix);

    //     foreach ($employee_matrix as $key => $mtx) {
    //       # code...
    //     }
    //   }
    // }
    // return $matrix;
    //new **********************


    foreach ($period as $key => $day_obj) {
      $day = $day_obj->format('d.m.Y');

      $rooms = 0;
      $total = 0;
      $rote = 0;
      $room_categories = config('constants.room_categories');
      $room_performs = config('constants.performs');
      $categories = [];
      $performs = [];
      foreach ($room_categories as $key => $category) {
        $categories[$key] = ['name' => $category, 'sum' => 0];
      }
      foreach ($room_performs as $key => $perform) {
        $performs[$key] = ['name' => $perform, 'id' => $key, 'employees' => 0, 'hours' => 0];
      }

      $filtered = $records->filter(function ($record) use ($day) {
        return Carbon::parse($record->time)->format('d.m.Y') == $day;
      });


      foreach ($filtered as $record) {
        foreach ($room_categories as $key => $category) {
          $sum = $record->rooms->where('category', $key)->count();
          // $sum = $record->calendar->rooms->where('category', $key)->where('pivot.record_id', $record->id)->count();
          $categories[$key]['sum'] += $sum;
          $matrix['grandtotal'][$category] += $sum;
          $total += $sum;
        }
        $rote += $record->rooms->where('pivot.status', 2)->count(); //red card
        // $rote += $record->calendar->rooms->where('pivot.status', 2)->where('pivot.record_id', $record->id)->count(); //red card

        $performs[$record->perform]['employees'] += 1;
        $matrix['grandtotal_performs'][$performs[$record->perform]['name']] += 1;
      }

      // calculate hours here:
      //heavy resource is used here
      foreach ($performs as $key => $perform) {
        $seconds = 0;
        foreach ($employees as $key => $employee) {
          $employee_records = $employee->records->filter(function ($record) use ($perform, $day_obj) {
            $today = $day_obj->format('d.m.Y');
            $tomorow = $day_obj->copy()->addDay()->endOfDay()->format('d.m.Y');
            return $record->perform == $perform['id'] &&
              (Carbon::parse($record->time)->format('d.m.Y') == $today || Carbon::parse($record->time)->format('d.m.Y') == $tomorow);
          });
          if ($employee_records->isEmpty()) {
            continue;
          }

          $temp_matrix = $this->generate_matrix($employee_records, $day_obj);
          $seconds += $this->calculate_hours($temp_matrix);
        }
        $performs[$perform['id']]['hours'] = number_format((float)($seconds / 3600), 2, '.', '');
        $matrix['grandtotal_performs_hours'][$perform['name']] += number_format((float)($seconds / 3600), 2, '.', '');
      }

      $matrix['days']->put($day, collect());
      $matrix['days'][$day]->put('rooms', $categories);
      $matrix['days'][$day]->put('performs', $performs);
      $matrix['days'][$day]->put('total', $total);
      $matrix['days'][$day]->put('rote', $rote);
      if ($reports->where('date', $day_obj->format('Y-m-d'))->first()) {
        $matrix['days'][$day]->put('reg', $reports->where('date', $day_obj->format('Y-m-d'))->first()->reg);
      } else {
        $matrix['days'][$day]->put('reg', 0);
      }
    }
    return $matrix;
  }

  // generate matrix for one employee for a period of time
  public function month_employee_matrix(CarbonPeriod $period, Collection $records)
  {

    $matrix = collect();
    foreach ($period as $key => $day_obj) {

      $record_row_counter = -1;
      $day = $day_obj->format('d.m.Y');
      $matrix->put($day, collect());
      $filtered = $records->filter(function ($record) use ($day) {
        return Carbon::parse($record->time)->format('d.m.Y') == $day;
      });

      $block_opened = false;
      foreach ($filtered as $record) {
        if ($record->action == 0) {
          $record_row_counter++;
          $block_opened = true;
        }

        if ($record->action != 0 && !$block_opened) {
          # day starts with record that is not checkin
          $yesterday = $day_obj->copy()->subDay()->format('d.m.Y');
          if (isset($matrix[$yesterday])) {
            $yesterday_last_key = $matrix[$yesterday]->reverse()->keys()->first();
            if ($matrix[$yesterday]->has($yesterday_last_key)) {
              $matrix[$yesterday][$yesterday_last_key]->push($record);
            }
          }
        } else {
          if (!$matrix[$day]->has($record_row_counter)) {
            $matrix[$day]->put($record_row_counter, collect());
          }
          $matrix[$day][$record_row_counter]->push($record);
        }
      }
    }
    return $matrix;
  }

  public function month_employee_matrix_aggregated(CarbonPeriod $period, Collection $records, Employee $employee)
  {

    $matrix = collect();
    $seconds = 0;
    $depas = 0;
    $restants = 0;
    $potential_minutes = 0;

    // return $this->month_employee_matrix($period, $records);

    foreach ($this->month_employee_matrix($period, $records) as $day => $records) {
      // $matrix->put( $day, collect() );
      // $matrix[$day]->put( $day, $records );

      // calculate work_time
      $seconds += $this->calculate_hours($records);
      // calculate depas and restants
      foreach ($records as $record_array) {
        foreach ($record_array as $record) {
          $depas += $record->rooms->where('pivot.clean_type', 0)->where('pivot.status', 1)->count();
          $restants += $record->rooms->where('pivot.clean_type', 1)->where('pivot.status', 1)->count();
          // calculate potential time
          $potential_minutes += $record->rooms->where('pivot.clean_type', 0)->where('pivot.status', 1)->sum('depa_minutes');
          $potential_minutes += $record->rooms->where('pivot.clean_type', 1)->where('pivot.status', 1)->sum('restant_minutes');
        }
      }
    }

    $matrix->put('id', $employee->id);
    $matrix->put('fullname', $employee->fullname);
    $matrix->put('function', $employee->function);
    $matrix->put('depas', $depas);
    $matrix->put('restants', $restants);
    $matrix->put('work_seconds', $seconds);
    $matrix->put('potential_seconds', $potential_minutes * 60);

    return $matrix;
  }
  
  public function daily_employee_matrix_aggregated(CarbonPeriod $period, Collection $records, Employee $employee)
  {
    $matrix = collect();
    $seconds = 0;
    $depas = 0;
    $restants = 0;
    $potential_minutes = 0;

    // Store daily data
    $daily_data = collect();

    foreach ($this->month_employee_matrix($period, $records) as $day => $day_records) {
      $day_seconds = $this->calculate_hours($day_records);
      $day_depas = 0;
      $day_restants = 0;
      $day_potential = 0;

      foreach ($day_records as $record_array) {
        foreach ($record_array as $record) {
          $day_depas += $record->rooms->where('pivot.clean_type', 0)->where('pivot.status', 1)->count();
          $day_restants += $record->rooms->where('pivot.clean_type', 1)->where('pivot.status', 1)->count();
          $day_potential += $record->rooms->where('pivot.clean_type', 0)->where('pivot.status', 1)->sum('depa_minutes');
          $day_potential += $record->rooms->where('pivot.clean_type', 1)->where('pivot.status', 1)->sum('restant_minutes');
        }
      }

      // Add to daily data
      $daily_data->put($day, [
        'depas' => $day_depas,
        'restants' => $day_restants,
        'work_seconds' => $day_seconds,
        'potential_seconds' => $day_potential * 60,
        'work_hours' => gmdate('H:i', $day_seconds),
        'potential_hours' => gmdate('H:i', $day_potential * 60)
      ]);

      // Add to totals
      $seconds += $day_seconds;
      $depas += $day_depas;
      $restants += $day_restants;
      $potential_minutes += $day_potential;
    }

    $matrix->put('id', $employee->id);
    $matrix->put('fullname', $employee->fullname);
    $matrix->put('function', $employee->function);
    $matrix->put('depas', $depas);
    $matrix->put('restants', $restants);
    $matrix->put('work_seconds', $seconds);
    $matrix->put('potential_seconds', $potential_minutes * 60);
    $matrix->put('work_hours', gmdate('H:i', $seconds));
    $matrix->put('potential_hours', gmdate('H:i', $potential_minutes * 60));
    $matrix->put('daily_data', $daily_data);

    return $matrix;
  }

  private function generate_matrix(Collection $records, Carbon $intended_date)
  {

    $record_row_counter = -1;
    $matrix = collect();
    $block_opened = false;
    $block_closed = false;

    // if ( $employee->records->isEmpty() ) {
    //   return $matrix;
    // }

    // $sorted_records = $employee->records->sortBy('time');
    $today = $records->first()->time;

    foreach ($records as $key_rec => $record) {

      if (!$today->isSameDay($intended_date) && $record_row_counter == -1) {
        // this prevents cases where ex. day is 21.05.2022
        //and there are no records on 21st but there are on 22nd
        break;
      }

      if ($block_closed && $today->format('Y-m-d') != $record->time->format('Y-m-d')) {
        // this prevents cases from getting checkins from tomorow, ie
        // day is 21.05.2022 and there are checkin(s) on 22nd
        break;
      }

      if ($record->action == 0) {
        $record_row_counter++;
        $block_opened = true;
        $block_closed = false;
      }
      if ($record->action == 1) {
        $block_opened = false;
        $block_closed = true;
      }

      $new_record = [
        'action' => $record->action,
        'time' => $record->time,
        'time_formatted' => $record->time_formatted,
        'id' => $record->id,
        'rooms' => collect($record->rooms),
        // 'rooms' => $record->calendar ? collect( $record->calendar->rooms->where('pivot.record_id', $record->id) ) : collect(),
      ];

      // if ( $block_opened ) {
      if ($record_row_counter >= 0) {
        if (!$matrix->has($record_row_counter)) {
          $matrix->put($record_row_counter, collect());
        }
        $matrix[$record_row_counter]->push((object)$new_record);
      }
      // }


    }

    return $matrix;
  }

  public function calculate_hours($matrix)
  {

    $grand_total_time = 0;
    foreach ($matrix as $day => $records) {
      $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
      $pausein_arr = $pauseout_arr = collect();
      foreach ($records as $key => $record) {
        //check in
        if ($record->action == 0) {
          $checkin_time = strtotime($record->time);
        }
        //pause in
        $pausin_records = $records->where('action', 2);
        $pausein_arr = $pausin_records->pluck('time');
        if ($pausin_records->count() > 0) {
          if ($pausin_records->count() > 1) {
            foreach ($pausin_records as $pin_record) {
              $pausein_time += strtotime($pin_record->time);
            }
          } else {
            $pausein_time = strtotime($pausin_records->first()->time);
          }
        }
        //pause out
        $pausout_records = $records->where('action', 3);
        $pauseout_arr = $pausout_records->pluck('time');
        if ($pausout_records->count() > 0) {
          if ($pausout_records->count() > 1) {
            foreach ($pausout_records as $pout_record) {
              $pauseout_time += strtotime($pout_record->time);
            }
          } else {
            $pauseout_time = strtotime($pausout_records->first()->time);
          }
        }
        //check out
        if ($record->action == 1) {
          $checkout_time = strtotime($records->firstWhere('action', 1)->time);
        }

        //calculate
        if ($checkin_time && $checkout_time) {
          if ($pausein_time && $pauseout_time) {
            $total_seconds = $checkout_time - $checkin_time - ($pauseout_time - $pausein_time);
          } else {
            $total_seconds = $checkout_time - $checkin_time;
          }
          $total_seconds_with_nightshift = RecordHelper::nightShiftHours($checkin_time, $pausein_arr, $pauseout_arr, $checkout_time, $total_seconds);
          if ($total_seconds_with_nightshift < 0) {
            $total_seconds_with_nightshift = 0;
          }
          $grand_total_time += $total_seconds_with_nightshift;
        }
      }
    }
    return $grand_total_time;
  }
}
