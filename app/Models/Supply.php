<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * The listings that belong to the Supply.
     */
    public function listings()
    {
        return $this->hasMany(SupplyListing::class);        
    }
}
