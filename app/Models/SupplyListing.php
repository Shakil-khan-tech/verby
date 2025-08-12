<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyListing extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['done', 'user_fixed', 'date_fixed'];

    /**
     * The listings that belong to the Supply.
     */
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Get the user that requested the listings.
     */
    public function userRequested()
    {
        return $this->belongsTo(User::class, 'user_requested');
    }

    /**
     * Get the user that requested the listings.
     */
    public function userFixed()
    {
        return $this->belongsTo(User::class, 'user_fixed');
    }

    /**
     * Get the user that owns the listings.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeActive($query)
    {
        $query->where('done', 0);
    }

    public function scopeFixed($query)
    {
        $query->where('done', 1);
    }
}
