<?php

namespace App\Policies;

use App\Models\Productquestionanswer;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class ProductquestionanswerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() or ($user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Productquestionanswer::class));

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Productquestionanswer $productquestionanswer): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Productquestionanswer::class);

    }


}
