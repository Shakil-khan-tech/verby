<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;
    
    protected $fillable = [
        // 'employee_id',
        'start',
        'end',
    ];

    // cast the start and end attributes to date
    protected $casts = [
        'start' => 'date',
        'end' => 'date',
    ];

    /**
     * Get the Employee that owns the entry.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
