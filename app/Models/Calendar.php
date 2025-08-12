<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    // public $timestamps = false;

    // protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'employee_id',
        'date',
        'user_id',
    ];

    /**
     * The rooms that belong to the Calendar.
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class)->using(CalendarRoom::class)
        ->withPivot('clean_type', 'extra', 'status', 'volunteer', 'record_id');
        // ->withTimestamps();
    }
    /**
     * Get the Rooms that the calendar has.
     */
    // public function rooms()
    // {
    //     return $this->hasMany('App\Models\Room');
    // }

    /**
     * Get the Records that the employee has.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Get the device that the Calendar belongs to.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * Get the employee that the Calendar belongs to.
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
