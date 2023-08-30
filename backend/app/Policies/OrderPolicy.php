<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() or $user->isSeller() or $user->isUser();
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
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() or $user->isSeller() or $user->isUser();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin() or $user->isSeller() or $user->isUser();
    }

    //////////////////////////////////////////////////////////

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyAdminOrSeller(User $user): bool
    {
        return ($user->isAdmin() and PermissionService::hasPermission($user, 'viewAny', Order::class)) || $user->isSeller();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewAdminOrSeller(User $user, Order $order): bool
    {
        return ($user->isAdmin() and PermissionService::hasPermission($user, 'view', Order::class)) || $user->isSeller();
    }

    /**
     * Determine whether the user can create models.
     */
    public function createAdminOrSeller(User $user): bool
    {
        return($user->isAdmin() and PermissionService::hasPermission($user, 'create', Order::class)) || $user->isSeller();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateAdminOrSeller(User $user, Order $order): bool
    {
        return ($user->isAdmin() and PermissionService::hasPermission($user, 'update', Order::class)) || $user->isSeller();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAdminOrSeller(User $user, Order $order): bool
    {
        return ($user->isAdmin() and PermissionService::hasPermission($user, 'delete', Order::class)) || $user->isSeller();
    }
}
