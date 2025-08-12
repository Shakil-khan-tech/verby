<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarReportFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'comment', 'employee_id', 'feedback'
    ];

    /**
     * Get the Employee that owns the entry.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
