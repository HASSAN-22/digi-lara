<?php

namespace App\Policies;

use App\Models\Debtor;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class DebtorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() or $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Debtor::class);

    }


    /**
     * Determine whether the user can create models.
     */
    public function pay(User $user, Debtor $debtor): bool
    {
        return $user->isSeller() and  $user->id == $debtor->user_id;
    }
}
