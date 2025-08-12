<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class DashboradController extends Controller
{
    /**
     * Display how many employees has entered and exited the company.
     *
     * @return \Illuminate\Http\Response
     */
    public function widgetDashboard(Request $request)
    {
        if ( !$request->ajax() ) {
          // return response()->json(['message' => "Just ajax requests!" ], 500);
        }
        $months = request('months') ? request('months') : 6;
        $from = Carbon::now()->subMonths($months-1)->firstOfMonth();
        $to = Carbon::now();
        $period = CarbonPeriod::create($from, '1 month', $to);
        $min = $max = 0;

        $enter = Employee::whereBetween('start', [$from, $to])
        ->select( DB::raw("DATE_FORMAT(`start`,'%Y-%m') as yearmonth"), DB::raw('count(id) as `total`') )
        ->groupby('yearmonth')
        ->get();

        $exit = Employee::whereBetween('end', [$from, $to])
        ->select( DB::raw("DATE_FORMAT(`end`,'%Y-%m') as yearmonth"), DB::raw('count(id) as `total`') )
        ->groupby('yearmonth')
        ->get();

        foreach ($period as $key => $month) {
          $categories[] = $month->format('M');
          $enter_data[$month->format('Y-m')] = 0;
          $exit_data[$month->format('Y-m')] = 0;
        }        

        foreach ($enter as $employees) {
          if ( array_key_exists( $employees->yearmonth, $enter_data ) ) {
            $enter_data[$employees->yearmonth] = $employees->total;
          }
        }

        foreach ($exit as $date => $employees) {
          if ( array_key_exists( $employees->yearmonth, $exit_data ) ) {
            $exit_data[$employees->yearmonth] = $employees->total;
          }
        }

        ksort($enter_data);
        ksort($exit_data);
        foreach ($enter_data as $d) {
          $enter_series[] = (int) $d;
        }
        foreach ($exit_data as $d) {
          $exit_series[] = (int) $d;
        }

        $min = min($enter_series) < min($exit_series) ? min($enter_series) : min($exit_series);
        $max = max($enter_series) > max($exit_series) ? max($enter_series) : max($exit_series);

        $series = [
          [
            'name' => __('Employees Enter'),
            'data' => $enter_series,
          ], [
            'name' => __('Employees Exit'),
            'data' => $exit_series,
          ]
        ];

        return response()->json( ['series' => $series, 'categories' => $categories, 'min' => $min, 'max' => $max, 'from'=>$from], 200 );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function widgetDashboard2(Request $request) //not used
    {
        if ( !$request->ajax() ) {
          // return response()->json(['message' => "Just ajax requests!" ], 500);
        }
        $months = request('months') ? request('months') : 6;
        $from = Carbon::now()->subMonths($months-1)->firstOfMonth();
        $to = Carbon::now();
        $period = CarbonPeriod::create($from, '1 month', $to);
        $min = $max = 0;

        $depa = Calendar::where('date', '>', $from)
          ->whereHas('rooms', function ($q) {
            $q->where('clean_type', 0);
          })
          ->withCount([
          'rooms' => function ($query) {
              $query->where('clean_type', 0);
          }])
          ->orderBy('date', 'asc')
          ->get()
          ->groupBy( function($date) {return Carbon::parse($date->date)->format('Y-m');} );

        $restant = Calendar::where('date', '>', $from)
          ->withCount('rooms')
          ->whereHas('rooms', function ($q) {
            $q->where('clean_type', 1);
          })
          ->withCount([
          'rooms' => function ($query) {
              $query->where('clean_type', 1);
          }])
          ->orderBy('date', 'asc')->get()
          ->groupBy( function($date) {return Carbon::parse($date->date)->format('Y-m');} );

        foreach ($period as $key => $month) {
          $categories[] = $month->format('M');
          $depa_data[$month->format('Y-m')] = 0;
          $restant_data[$month->format('Y-m')] = 0;
          foreach ($depa as $date => $calendars) {
            $sum = 0;
            foreach ($calendars as $key => $calendar) {
              $sum += $calendar->rooms_count;
            }
            $depa_data[$date] = $sum;
          }
          foreach ($restant as $date => $calendars) {
            $sum = 0;
            foreach ($calendars as $key => $calendar) {
              $sum += $calendar->rooms_count;
            }
            $restant_data[$date] = $sum;
          }
        }

        ksort($depa_data);
        ksort($restant_data);
        foreach ($depa_data as $d) {
          $depa_series[] = (int) $d;
        }
        foreach ($restant_data as $d) {
          $restant_series[] = (int) $d;
        }

        $min = min($depa_series) < min($restant_series) ? min($depa_series) : min($restant_series);
        $max = max($depa_series) > max($restant_series) ? max($depa_series) : max($restant_series);

        $series = [
          [
            'name' => 'Depa',
            'data' => $depa_series,
          ], [
            'name' => 'Restant',
            'data' => $restant_series,
          ]
        ];

        return response()->json( ['series' => $series, 'categories' => $categories, 'min' => $min, 'max' => $max, 'from'=>$from], 200 );
    }
}
