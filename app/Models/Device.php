<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;
use DB;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the Employees that the device belongs to.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
    /*
    * Get Employees Exclude Canceled Status
    */
    public function excludeCenceledEmployees()
    {
        return $this->belongsToMany(Employee::class)
            ->where('function', '!=', 6);
    }

    /**
     * Get the Records that the device has.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Device');
    }

    /**
     * Get the Rooms that the device has.
     */
    public function rooms()
    {
        return $this->hasMany('App\Models\Room');
    }

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that belong to the Device.
     */
    public function users()
    {
        // return $this->belongsToMany(User::class);
        return $this->belongsToMany(User::class)->using(DeviceUser::class);
    }

    /**
     * Get the Monthly Reports that the device has.
     */
    public function monthly_reports()
    {
        return $this->hasMany('App\Models\MonthlyReport');
    }

    /**
     * Scope a query to only include devices that the user has acces to.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeAvailable($query)
    {
        if (Auth::user()->hasRole(['super_admin', 'admin'])) {
            return $query;
        }

        $user_id = Auth::user()->id;
        return $query->whereHas('users', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        });
    }


    public function validEmployees()
    {
        $rel = $this->belongsToMany(Employee::class, 'device_employee', 'device_id', 'employee_id')
            ->where('function', '!=', 6);

        if (config('app.plan_contracts_gating', env('PLAN_CONTRACTS_GATING', false))) {
            $rel = $rel->withAllContractsSigned();
        }

        return $rel;
    }
}
