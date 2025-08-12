<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'pushimi';

    /**
     * Get the employee that the Plan belongs to.
     */
    public function employee()
    {
        // return $this->belongsTo('App\Models\Employee', 'userid')->orderBy('data');
        return $this->belongsTo('App\Models\Employee')->orderBy('data');
    }
}
