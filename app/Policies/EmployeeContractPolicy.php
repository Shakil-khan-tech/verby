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
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('view_contracts') || $user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EmployeeContract $employeeContract)
    {
        if ($user->can('view_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EmployeeContract $employeeContract)
    {
        if ($user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EmployeeContract $employeeContract)
    {
        if ($user->can('manage_contracts')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EmployeeContract $employeeContract)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeContract  $employeeContract
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EmployeeContract $employeeContract)
    {
        //
    }
}
