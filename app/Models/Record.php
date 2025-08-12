<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// use Debugbar;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecordCheckinSuspicious;

class Record extends Model
{
    use HasFactory;

    protected $dates = ['time'];
    protected $fillable = ['employee_id', 'device_id', 'perform', 'user_id', 'identity', 'action', 'time'];
    protected $appends = ['rooms'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($record) {
            if ($record->action == 1) { //checkout
                
                $from = Carbon::parse($record->time)->startOfDay();
                $to = $from->copy()->endOfDay();
                $e_id = $record->employee_id;
                // Debugbar::addMessage($from, 'FROM');
                // Debugbar::addMessage($to, 'TO');
                // $records = Record::where('action', '!=', 1)->whereBetween('time', [$from, $to])->get(); //skip checkout
                $checkin_record = Record::where('action', 0)->whereBetween( 'time', [$from, $to] )->where('employee_id', $e_id)->latest('time')->first();
                $pausein_record = Record::where('action', 2)->whereBetween( 'time', [$from, $to] )->where('employee_id', $e_id)->latest('time')->first();
                $pauseout_record = Record::where('action', 3)->whereBetween( 'time', [$from, $to] )->where('employee_id', $e_id)->latest('time')->first();
                // Debugbar::addMessage($checkin_record, 'CHECK IN');
                // Debugbar::addMessage($record, 'CHECK OUT');

                if ($checkin_record !== null) {
                    $checkin_time = $checkin_record->time;
                    $checkout_time = $record->time;
                    $work_time = $checkout_time->diffInMinutes($checkin_time);
                    // Debugbar::warning( $checkin_time );
                    // Debugbar::warning( $checkout_time );
                    // Debugbar::warning( $work_time );
                    if ( $work_time < 330 ) { //5.5 hours
                        // Debugbar::warning( $work_time );
                        return; // no need for checking, exit!
                    }

                    // determine pause to add - start
                    $pause_time = 0; //in minutes
                    if ( $work_time < 420 ) { //7 hours
                        // Debugbar::addMessage('<7', 'labela');
                        $pause_time = 15;
                    } elseif ( $work_time < 540 ) { //9 hours
                        // Debugbar::addMessage('<9', 'labela');
                        $pause_time = 30;
                    } elseif ( $work_time >= 540 ) { //9 hours
                        // Debugbar::addMessage('>=9', 'labela');
                        $pause_time = 60;
                    }
                    // determine pause to add - end

                    if ($pausein_record !== null && $pauseout_record !== null) {
                        //modify pauses
                        // Debugbar::addMessage('Enter 1', 'labela');
                        $current_pause = $pauseout_record->time->diffInMinutes( $pausein_record->time );
                        if ( $current_pause < $pause_time ) {
                            $pauseout_record->time = $pausein_record->time->addMinutes( $pause_time );
                            $pauseout_record->save();
                        }
                    }

                    if ($pausein_record === null && $pauseout_record === null) {
                        //create both pauses
                        // Debugbar::addMessage('Enter 2', 'labela');
                        // Debugbar::addMessage($work_time, 'work time');
                        // Debugbar::addMessage($pause_time, 'pause time');
                        $p_in = Record::create([
                            'employee_id' => $record->employee_id,
                            'device_id' => $record->device_id,
                            'perform' => $record->perform,
                            'user_id' => auth()->user()->id,
                            'identity' => 3, //PC
                            'action' => 2,
                            'time' => $record->time->subMinutes( $work_time / 2),
                        ]);
                        $p_out = Record::create([
                            'employee_id' => $record->employee_id,
                            'device_id' => $record->device_id,
                            'perform' => $record->perform,
                            'user_id' => auth()->user()->id,
                            'identity' => 3, //PC
                            'action' => 3,
                            'time' => $p_in->time->addMinutes($pause_time),
                        ]);
                        // Debugbar::addMessage($p_in->time, 'pre');
                        // Debugbar::addMessage($p_in->time->addMinutes($pause_time) , 'after');
                        // Debugbar::info($p_in);
                        // Debugbar::info($p_out);
                    }
                    if ($pausein_record === null && $pauseout_record !== null) {
                        //create pause in
                        // Debugbar::addMessage('Enter 3', 'labela');
                        $p_in = Record::create([
                            'employee_id' => $record->employee_id,
                            'device_id' => $record->device_id,
                            'perform' => $record->perform,
                            'user_id' => auth()->user()->id,
                            'identity' => 3, //PC
                            'action' => 3,
                            'time' => $pauseout_record->time->subMinutes($pause_time),
                        ]);
                    }
                    if ($pausein_record !== null && $pauseout_record === null) {
                        //create pause out
                        // Debugbar::addMessage('Enter 4', 'labela');
                        $p_out = Record::create([
                            'employee_id' => $record->employee_id,
                            'device_id' => $record->device_id,
                            'perform' => $record->perform,
                            'user_id' => auth()->user()->id,
                            'identity' => 3, //PC
                            'action' => 3,
                            'time' => $pausein_record->time->addMinutes($pause_time),
                        ]);
                    }
                }
            }

            if ($record->action == 0) { //checkin
                $from = Carbon::parse($record->time)->subHour();
                $to = Carbon::parse($record->time);
                $e_id = $record->employee_id;
                
                $checkout_record = Record::where('action', 1)->whereBetween( 'time', [$from, $to] )->where('employee_id', $e_id)->latest('time')->first();
                
                if ($checkout_record !== null) {
                    $employee = $record->employee;
                    // Debugbar::addMessage($employee, 'Suspicious');
                    $a = $employee->name;
                    $b = $record->time;
                    $c = $checkout_record->time;
                    Mail::to( config('mail.recipient.address') )->send( new RecordCheckinSuspicious( $employee, $record->time, $checkout_record->time ) );
                }
            }
        });
    }

    /**
     * Get the Employee that the record has.
     */
     public function employee()
     {
         return $this->belongsTo('App\Models\Employee');
     }


    /**
     * Get the Devices that the record has.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * The rooms that belong to the Record.
     */
    // public function rooms()
    // {
    //     return $this->belongsToMany(Room::class, CalendarRoom::class)
    //     ->where('calendar_room.calendar_id', '=', $this->calendar_id)
    //     ->withPivot('clean_type', 'extra', 'status', 'volunteer');
    // }

    /**
     * Get all of the rooms for the record.
     */
    public function getRoomsAttribute()
    {
        return $this->calendar ? $this->calendar->rooms->where('pivot.record_id', '=', $this->id) : collect();
    }

    /**
     * Get the Calendar that the record has.
     */
    public function calendar()
    {
        return $this->belongsTo('App\Models\Calendar');
    }

    /**
     * Get the user that the Record belongs to (nullable).
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the records's time formatted.
     *
     * @return string
     */
    public function getTimeFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->time)->format('H:i:s');
        // return \Carbon\Carbon::parse($this->time)->format('d.m.Y H:i:s');
    }

    // public function volunteer()
    // {
    //     return $this->belongsTo(Employee::class, 'employee_id')->using(RecordRoom::class)
    //     ->withPivot('employee_id')
    //     ->wherePivotNotNull('employee_id');
    // }
}
