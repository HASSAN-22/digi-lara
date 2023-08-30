<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdrawwallet;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class WithdrawwalletPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->isUser() or $user->isSeller()) or ($user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Withdrawwallet::class));

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Withdrawwallet $withdrawwallet): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUser() or $user->isSeller();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Withdrawwallet $withdrawwallet): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Withdrawwallet::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Withdrawwallet $withdrawwallet): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Withdrawwallet $withdrawwallet): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Withdrawwallet $withdrawwallet): bool
    {
        //
    }
}
