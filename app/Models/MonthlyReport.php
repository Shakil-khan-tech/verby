<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
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
        'date',
        'reg',
        'rote',
    ];

    /**
     * Get the device that the Monthly Report belongs to.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
    
}
