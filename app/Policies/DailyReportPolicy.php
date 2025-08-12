<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DailyReportPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can manage daily reports.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function manage(User $user)
    {
        if ($user->can('manage_daily_reports')) {
            return true;
        }
    }
}
