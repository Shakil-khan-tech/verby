<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    public function manage_reminder(User $user)
    {
        if ($user->can('manage_reminder')) {
            return true;
        }
    }
}
