<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lohn extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'lohnabrechnung';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timestamp_nice',
    ];

    /**
     * Get the user's DOB formatted.
     *
     * @return string
     */
    public function getTimestampNiceAttribute($value)
    {
        return date( "Y-m-d", strtotime($value) );
    }

}
