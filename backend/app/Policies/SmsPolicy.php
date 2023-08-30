<?php

namespace App\Policies;

use App\Models\Sms;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class SmsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Sms::class);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Sms::class);

    }
}
