<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Support\Str;

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * Get the permissions's title case.
     *
     * @return string
     */
    public function getNiceNameAttribute()
    {
        return Str::title( str_replace('_', ' ', $this->name) );
    }
}
