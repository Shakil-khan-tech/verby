<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * The roomlists that belong to the Issue.
     */
    public function listings()
    {
        return $this->hasMany(IssueListing::class);        
    }
}
