<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, News::class);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, News $news): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, News::class);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, News::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, News $news): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, News::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, News $news): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, News::class);
    }

}
