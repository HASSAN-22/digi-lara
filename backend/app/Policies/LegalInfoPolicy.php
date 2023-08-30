<?php

namespace App\Policies;

use App\Models\Legalinfo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LegalInfoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Legalinfo $legalinfo): bool
    {
        //
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
    public function update(User $user, Legalinfo $legalinfo): bool
    {
        return ($user->isAdmin() or $user->isSeller() or $user->isUser()) and $user->id == $legalinfo->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Legalinfo $legalinfo): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Legalinfo $legalinfo): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Legalinfo $legalinfo): bool
    {
        //
    }
}
