<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the Records that the user has created/updated.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Get the device associated with the user.
     */
    public function device()
    {
        return $this->hasOne(Device::class);
    }

    /**
     * The devices that belong to the user.
     */
    public function devices()
    {
        // return $this->belongsToMany(Device::class);
        
        return $this->belongsToMany(Device::class)->using(DeviceUser::class);
        // ->whereIn('device.id', ['device_user.id']);
    }

    /**
     * Scope a query to only include real users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoDevice($query)
    {
        return $query->where('is_device', 0);
    }

    /**
     * Scope a query to only include devices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDevice($query)
    {
        return $query->where('is_device', 1);
    }

    /**
     * Get the user's DOB formatted.
     *
     * @return string
     */
    public function getDOBAttribute($value)
    {
        return date( "Y-m-d", strtotime($value) );
    }

    /**
     * Get the user's start date formatted.
     *
     * @return string
     */
    public function getStartAttribute($value)
    {
        return date( "Y-m-d", strtotime($value) );
    }

    /**
     * Get the user's end date formatted.
     *
     * @return string
     */
    public function getEndAttribute($value)
    {
        return date( "Y-m-d", strtotime($value) );
    }

    /**
     * Get the users's avatar path.
     *
     * @return string
     */
    public function getAvatarPathAttribute()
    {
        if ( $this->avatar ) {
          return 'storage/' . $this->avatar;
        } else {
          return 'media/users/blank.png';
        }
    }
}
