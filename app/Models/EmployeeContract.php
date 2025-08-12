<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'employee_id',
        'name',
        'file_name',
        'mime_type',
        'size',
        'is_sign',
    ];
}
