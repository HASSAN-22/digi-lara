<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->isSeller() or $user->isUser()) or ($user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Ticket::class));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return (($user->isSeller() or $user->isUser()) and $user->id == $ticket->user_id) or
            ($user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Ticket::class));

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSeller() or $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Ticket::class);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        //
    }

    /**
     * Determine whether the user can send message the model.
     */
    public function sendMessage(User $user, Ticket $ticket): bool
    {
        return (($user->isSeller() or $user->isUser()) and $user->id == $ticket->user_id) or
            ($user->isAdmin() and PermissionService::hasPermission($user, 'create', Ticket::class));
    }

}
