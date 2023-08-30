<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class PropertyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Property::class);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Property::class);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Property::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Property::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Property::class);
    }
}
