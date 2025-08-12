<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage calendars.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
     public function manage(User $user)
     {
         if ($user->can('manage_calendars')) {
             return true;
         }
     }
}
