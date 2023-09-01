<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Returned;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class ReturnedPolicy
{
    public function view(User $user, Order $order){
        return ($user->isUser() or $user->isSeller() or $user->isAdmin()) and $user->id == $order->user_id;
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUser() or $user->isSeller() or $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Returned $returned): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Returned::class);

    }

}
