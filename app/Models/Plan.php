<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // public $timestamps = false;

    protected $table = 'plani';

    protected $fillable = ['employee_id', 'device_id', 'dita', 'symbol'];

    /**
     * Get the employee that the Plan belongs to.
     */
    public function employee()
    {
        // return $this->belongsTo('App\Models\Employee', 'userid')->orderBy('roli');
        return $this->belongsTo('App\Models\Employee')->orderBy('function');
    }

    /**
     * Get the plans's day of week
     *
     * @return string
     */
    public function getDayOfWeekAttribute($value)
    {
        return $value->dayOfWeek;
    }

}
