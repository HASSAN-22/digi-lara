<?php

namespace App\Policies;

use App\Models\Becomeseller;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class BecomesellerPollicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isUser() or $user->isSeller();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Becomeseller $becomeseller): bool
    {
        return ($user->isUser() or $user->isSeller()) and $user->id == $becomeseller->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Becomeseller $becomeseller): bool
    {
        return $user->isSeller() and $user->id == $becomeseller->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Becomeseller $becomeseller): bool
    {
        //
    }

    //////////////////////////////////////////

    /**
     * Determine whether the user can view the model.
     */
    public function viewAdmin(User $user, Becomeseller $becomeseller): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, 'view', Becomeseller::class);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyAdmin(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, 'viewAny', Becomeseller::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateAdmin(User $user, Becomeseller $becomeseller): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, 'update', Becomeseller::class);
    }
}
