<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() or $user->isAdmin() or $user->isUser();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Profile $profile): bool
    {
        return ($user->isSeller() or $user->isAdmin() or $user->isUser()) and $user->id == $profile->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSeller() or $user->isAdmin() or $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Profile $profile): bool
    {
        return ($user->isSeller() or $user->isAdmin() or $user->isUser()) and $user->id == $profile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Profile $profile): bool
    {
        return ($user->isSeller() or $user->isAdmin() or $user->isUser()) and $user->id == $profile->user_id;
    }

}
