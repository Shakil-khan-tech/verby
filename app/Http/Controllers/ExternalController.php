<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Employee;
use App\Models\Device;
use App\Models\Plan;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Validator;
use Illuminate\Support\Facades\URL;

use App\Http\Traits\RecordTrait;
use App\Jobs\SendFeedbackMailJob;

class ExternalController extends Controller
{
    use RecordTrait;
    
    public function __construct()
    {
        $this->middleware('signed');
    }

    /**
     * Get records for an employee.
     *
     * @return json
     */
    public function records_report(Employee $employee, Request $request)
    {
        $from = Carbon::now()->firstOfMonth()->startOfDay();
        $to = $from->copy()->lastOfMonth()->endOfDay();
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->firstOfMonth()->startOfDay();
              $to = $from->copy()->lastOfMonth()->endOfDay();
          } catch (\Exception $e) {
              // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }
        $month = $from->format('Y-m');
        $expiration = request('expiration') ? Carbon::parse( request('expiration') ) : 'no expiration';

        $page_title = __('Individual Monthly Performance for ') . $from->format('m.Y');
        $page_description = __('Employee: ') . $employee->fullname;
        $devices = Device::all();

        $employee_records = Record::where('employee_id', $employee->id)
        // ->whereBetween( 'time', [$start, $end] )
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'time', [$from, $to] );
        })
        ->select('id', 'device_id', 'action', 'time', 'perform', 'identity')
        ->with('device')
        ->with('calendar.rooms:id,name,category,depa_minutes,restant_minutes')
        ->orderBy('time', 'ASC')
        ->get();

        // return $employee_records;

        $period = CarbonPeriod::create($from, '1 day', $to);
        /*matrix*/
        $matrix = $this->month_employee_matrix($period, $employee_records);

        $plans = Plan::where('employee_id', $employee->id)
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'dita', [$from, $to] );
            // ->whereIn( 'symbol', ['K','U','F'] );
        })
        ->select('id', 'symbol', 'dita')
        ->get();

        $plans->map(function ($plan) {
          $plan->dita_formatted = Carbon::parse($plan->dita)->format('d.m.Y');
          return $plan;
        });

        $feedback_url = URL::signedRoute('external.records_report_feedback', ['employee' => $employee->id, 'date' => $month]);

        //if expiration expired, return 404
        if ( $expiration != 'no expiration' && Carbon::now()->gt($expiration) ) {
            $employee_feedback = 2;
            $employee_feedback_date = $expiration;
        } else {
            // if expiration is not expired, get employee feedback for the month, if exists
            $employee_feedback = $employee->calendar_report_feedback->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])->sortBy('created_at')->last()?->feedback;
            $employee_feedback_date = $employee->calendar_report_feedback->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])->sortBy('created_at')->last()?->date;
        }

        return view('pages.external.records_report', compact(
            'page_title', 'page_description', 'employee_records', 'period', 'month', 'matrix', 'employee', 'devices', 'plans', 'feedback_url', 'employee_feedback', 'employee_feedback_date', 'expiration'
        ));
    }

    /**
     * Save employee's feedback for a specific month.
     *
     * @return json
     */
    public function records_report_feedback(Employee $employee, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'feedback' => 'required|boolean',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => "Cannot save feedback", 'errors' => $validator->errors()], 500);
        }

        $feedback = $employee->calendar_report_feedback()->create([
            'date' => Carbon::parse( request('date') )->format('Y-m-d'),
            'feedback' => request('feedback'),
            'comment' => request('comment')
        ]);

        if ( request('feedback') == 0 ) {
            $url = route('records.calendar_print', ['employee' => $employee->id, 'date' => Carbon::parse( request('date') )->format('Y-m')]);
            $month = Carbon::parse( request('date') )->format('m.Y');
            $comment = request('comment') ? request('comment') : 'No comment';
            $locale = app()->getLocale();
            SendFeedbackMailJob::dispatch($url, $month, $comment, $locale)->delay(now()->addSeconds(1));
        }

        return response()->json(['message' => "Feedback saved", 'feedback' => $feedback], 200);
    }

}
