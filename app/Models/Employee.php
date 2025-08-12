<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Auth;

class Employee extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $appends = ['fullname', 'active_status', 'entries_status'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'DOB',
        'email',
        'password',
    ];

    /**
     * Get the devices that the Employee belongs to.
     */
    public function devices()
    {
        return $this->belongsToMany(Device::class);
    }

    /**
     * Get the Plans that the employee has.
     */
    public function plans()
    {
        // return $this->hasMany('App\Models\Plan', 'userid', 'userid');
        return $this->hasMany('App\Models\Plan');
    }

    /**
     * Get the Vacations that the employee has.
     */
    public function vacations()
    {
        // return $this->hasMany('App\Models\Vacation', 'userid', 'userid');
        return $this->hasMany('App\Models\Vacation');
    }

    /**
     * Get the Records that the employee has.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Get the Calendars that the employee has.
     */
    public function calendars()
    {
        return $this->hasMany('App\Models\Calendar');
    }

    /**
     * Get the Entries that the employee has.
     */
    public function entries()
    {
        return $this->hasMany('App\Models\Entry')->orderBy('id', 'DESC');
    }

    /**
     * Get the Entries that the employee has.
     */
    public function calendar_report_feedback()
    {
        return $this->hasMany(CalendarReportFeedback::class);
    }

    /**
     * Get the employee's DOB formatted.
     *
     * @return string
     */
    public function getDOBAttribute($value)
    {
        return $value ? date("Y-m-d", strtotime($value)) : null;
        // return date( "Y-m-d", strtotime($value) );
    }

    /**
     * Get the employee's start date formatted.
     *
     * @return string
     */
    public function getStartAttribute($value)
    {
        return $value ? Carbon::parse($value)->startOfDay()->format('Y-m-d') : null;
    }

    /**
     * Get the employee's end date formatted.
     *
     * @return string
     */
    public function getEndAttribute($value)
    {
        return $value ? Carbon::parse($value)->endOfDay()->format('Y-m-d') : null;
    }

    /**
     * Get the employee's fullname: name + surname.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return $this->name . ' ' . $this->surname;
    }

    // Get employees that are active
    public function scopeActive($query)
    {
        return $query->whereDate('start', '<=', Carbon::now())
            ->where(function ($q) {
                $q->whereDate('end', '>=', Carbon::now())
                    ->orWhereNull('end');
            });
    }

    // Get employees that are inactive
    public function scopeInactive($query)
    {
        return $query->whereDate('start', '>=', Carbon::now())
            ->orWhereDate('end', '<=', Carbon::now()->endOfDay());
    }

    // Get employees that are active based on a start & end date
    public function scopeActiveBetween($query, $start, $end)
    {
        return $query->whereDate('start', '<=', $end)
            ->where(function ($q) use ($start, $end) {
                $q->whereDate('end', '>=', $start)
                    ->orWhereNull('end');
            });
    }

    // Get employees that are inactive based on a start & end date
    public function scopeInactiveBetween($query, $start, $end)
    {
        return $query->whereDate('start', '>=', $end)
            ->orWhereDate('end', '<=', $start);
    }

    // Get emoployees that are in devices that the user has access to
    public function scopeInDevices($query, $devices)
    {
        if (Auth::user()->hasRole(['super_admin', 'admin'])) {
            return $query;
        }

        return $query->whereHas('devices', function ($q) use ($devices) {
            $q->whereIn('id', $devices);
        });
    }

    /**
     * Get the employee's active status: end > now.
     *
     * @return string
     */
    public function getActiveStatusAttribute()
    {
        return $this->start <= Carbon::now() && ($this->end >= Carbon::now()->startOfDay()->format('Y-m-d') || $this->end == null) ? 1 : 0;
    }

    public function getEntriesStatusAttribute()
    {
        return $this->entries()->count() > 0 ? 1 : 0;
    }

    public function getCombinedEntriesAttribute()
    {
        $entries = $this->entries()->select('start', 'end')->get();
        $start_end = collect(['start' => $this->start, 'end' => $this->end]);
        $entries->push($start_end);
        // $entries = $entries->sortBy('start')->values()->all();
        return $entries;
    }

    // app/Models/Employee.php

    public function scopeWithAllContractsSigned($query)
    {
        if (!config('app.plan_contracts_gating', env('PLAN_CONTRACTS_GATING', false))) {
            return $query;
        }
        return $query->where(function ($query) {
            $query->where('employee_type', 0)
                ->orWhere(function ($q) {
                    $q->where('employee_type', 1)
                        ->whereRaw('
                        (SELECT COUNT(*) FROM contracts) = (
                            SELECT COUNT(*) 
                            FROM employee_contracts 
                            WHERE employee_contracts.employee_id = employees.id 
                            AND is_sign = 1
                        )');
                });
        });
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function remindercontracts()
    {
       return $this->belongsToMany(Contract::class, 'employee_contracts')
                ->withPivot('is_sign')
                ->withTimestamps();
    }

    
}
