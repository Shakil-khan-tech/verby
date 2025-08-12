<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        if ($user->can('view_users') || $user->can('manage_users')) {
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
        if ($user->can('manage_users')) {
            return true;
        }
    }

    /**
     * Determine whether the user can show models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function show(User $user, User $model)
    {
        if ( $model->hasRole('super_admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ( $model->hasRole('admin') && !$user->hasRole('admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ($user->can('manage_users')) {
            return true;
        }
    }

    /**
     * Determine whether the user can edit models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function edit(User $user, User $model)
    {
        if ( $model->hasRole('super_admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ( $model->hasRole('admin') && !$user->hasRole('admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ($user->can('manage_users')) {
            return true;
        }
    }

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function manage(User $user, User $model)
    {
        if ( $model->hasRole('super_admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ( $model->hasRole('admin') && !$user->hasRole('admin') && !$user->hasRole('super_admin') ) {
          return false;
        }
        if ($user->can('manage_users')) {
            return true;
        }
    }

}
