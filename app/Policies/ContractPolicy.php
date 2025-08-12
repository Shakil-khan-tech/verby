<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
{
    use HandlesAuthorization;

    public function contract(User $user)
    {
        if ($user->can('contract')) {
            return true;
        }
    }
}
