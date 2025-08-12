<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CalendarRoom extends Pivot
{
    use HasFactory;

    protected $appends = ['volunteer_name'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($calendar_room) {
            if ($calendar_room->status == 0) { //uncleaned
                $calendar_room->volunteer = null;
                $calendar_room->record_id = null;
                $calendar_room->saveQuietly();
                return true;
            }
        });

        static::created(function ($calendar_room) {
            if ($calendar_room->status == 0) { //uncleaned
                $calendar_room->volunteer = null;
                $calendar_room->record_id = null;
                $calendar_room->saveQuietly();
                return true;
            }
        });
    }

    /**
     * Get the Employee that the record has.
     */
     public function volunteer()
     {
         return $this->belongsTo('App\Models\Employee', 'volunteer');
     }


    /**
     * Get the Devices that the record has.
     */
    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    /**
     * Get the Devices that the record has.
     */
    public function calendar()
    {
        return $this->belongsTo('App\Models\Calendar');
    }

    /**
     * Get the Devices that the record has.
     */
    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }

    public function getVolunteerNameAttribute()
    {
        if ( $this->volunteer ) {
            return \App\Models\Employee::find($this->volunteer)->full_name;
        }
        return "";
    }

}
