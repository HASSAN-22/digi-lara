<?php

namespace App\Policies;

use App\Models\Newsletter;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class NewsletterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Newsletter::class);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Newsletter $newsletter): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Newsletter::class);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Newsletter::class);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Newsletter $newsletter): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Newsletter::class);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Newsletter $newsletter): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Newsletter::class);

    }

}
