<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class WalletPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() or $user->isSeller() or $user->isUser();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wallet $wallet): bool
    {
        return ($user->isAdmin() or $user->isSeller() or $user->isUser()) and $wallet->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() or $user->isSeller() or $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wallet $wallet): bool
    {
        return ($user->isAdmin() or $user->isSeller() or $user->isUser()) and $wallet->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wallet $wallet): bool
    {
        return ($user->isAdmin() or $user->isSeller() or $user->isUser()) and $wallet->user_id == $user->id;
    }

    //////////////////////////////////////////////////////////////////////////

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyAdmin(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Wallet::class);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewAdmin(User $user, Wallet $wallet): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Wallet::class);
    }

    /**
     * Determine whether the user can create models.
     */
    public function createAdmin(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Wallet::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateAdmin(User $user, Wallet $wallet): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Wallet::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAdmin(User $user, Wallet $wallet): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Wallet::class);
    }

}
