<?php

namespace App\Policies;

use App\Models\Lohn;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage payrolls.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function manage(User $user)
    {
        if ($user->can('manage_payrolls')) {
            return true;
        }
    }
}
