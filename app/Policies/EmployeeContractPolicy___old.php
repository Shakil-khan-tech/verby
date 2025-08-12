<?php

namespace App\Policies;

use App\Models\EmployeeContract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->can('view_contracts') || $user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return mixed
     */
    public function manage(User $user, EmployeeContract $employeeContract)
    {
        if ($user->can('manage_contracts')) {
            return true;
        }
    }
}
