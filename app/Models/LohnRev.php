<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Carbon\Carbon;

class LohnRev extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'lohnabrechnung_revisions';

    // protected $guarded = ['id', 'timestamp'];
    protected $guarded = ['id'];

    // protected $dates = ['timestamp'];

    /**
     * Change the timezone.
     *
     * @return string
     */
    // public function getTimestampAttribute($value)
    // {
    //     return Carbon::parse($value)->setTimezone('Europe/Zurich');
    // }

}
