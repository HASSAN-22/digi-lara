<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, User::class);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, User::class);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, User::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, User::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, User::class);
    }

    ////////////////////// Seller ////////////////////////

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnySeller(User $user): bool
    {
        return $user->isSeller();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewSeller(User $user, User $model): bool
    {
        return $user->isSeller() and $model->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function createSeller(User $user): bool
    {
        return $user->isSeller();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateSeller(User $user, User $model): bool
    {
        return $user->isSeller() and $model->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteSeller(User $user, User $model): bool
    {
        return $user->isSeller() and $model->user_id == $user->id;
    }


}
