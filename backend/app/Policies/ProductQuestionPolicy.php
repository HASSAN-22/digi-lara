<?php

namespace App\Policies;

use App\Models\Productquestion;
use App\Models\Productquestionanswer;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class ProductQuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() or ($user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Productquestion::class));

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Productquestion $productquestion): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Productquestion::class);

    }



    /**
     * Determine whether the user can update the model.
     */
    public function createAnswer(User $user, Productquestion $productquestion): bool
    {
        return $user->isSeller() and $user->id == $productquestion->product->user_id;

    }
}
