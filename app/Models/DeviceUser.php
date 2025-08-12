<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DeviceUser extends Pivot
{
    use HasFactory;

    /**
     * Get the Devices that the record has.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * Get the Devices that the record has.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
