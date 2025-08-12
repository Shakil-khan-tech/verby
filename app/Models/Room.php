<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
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
        'name',
        'category',
        'depa_minutes',
        'restant_minutes',
    ];

    /**
     * The calendars that belong to the room.
     */
    public function calendars()
    {
        return $this->belongsToMany('App\Models\Calendar')
        ->withPivot('clean_type', 'extra');
        // ->withTimestamps();
        
    }

    /**
     * The calendars that belong to the room.
     */
    public function records()
    {
        return $this->belongsToMany('App\Models\Record')
        ->withPivot('clean_type', 'extra');
        // ->withTimestamps();
    }

    /**
     * Get the device that the Room belongs to.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
    

    // public function volunteer()
    // {
    //     return $this->belongsTo(Employee::class, 'employee_id')->using(RecordRoom::class)
    //     ->withPivot('employee_id')
    //     ->wherePivotNotNull('employee_id');
    // }
    
}
