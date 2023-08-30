<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class BrandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Brand::class);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Brand $brand): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Brand::class);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Brand::class);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Brand $brand): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Brand::class);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Brand $brand): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Brand::class);

    }

    /////////////////////////////////////////////// Seller /////////////////////////////////////////////////////

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
    public function viewSeller(User $user, Brand $brand): bool
    {
        return $user->isSeller() and $brand->user_id == $user->id;

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
    public function updateSeller(User $user, Brand $brand): bool
    {
        return $user->isSeller() and $brand->user_id == $user->id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteSeller(User $user, Brand $brand): bool
    {
        return $user->isSeller() and $brand->user_id == $user->id;

    }

}
