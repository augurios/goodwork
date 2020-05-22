<?php

namespace App\Ticket\Policies;

use App\Base\Models\User;
use App\Authorization\Authorization;
use App\Ticket\Models\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tickets list.
     *
     * @param  \App\Base\Models\User $user
     * @return mixed
     */
    public function list(User $user)
    {
        return (new Authorization($user))->userIsMemberOfGroup(request('group_type'), request('group_id'));
    }

    /**
     * Determine whether the user can view the app models ticket.
     *
     * @param  \App\Base\Models\User             $user
     * @param  \App\Ticket\Models\Ticket $ticket
     * @return mixed
     */
    public function view(User $user, Ticket $ticket)
    {
        return (new Authorization($user))->userHasPermissionTo('view', 'ticket', $ticket->id, true, request('group_type'), request('group_id'));
    }

    /**
     * Determine whether the user can update the app models ticket.
     *
     * @param  \App\Base\Models\User             $user
     * @param  \App\Ticket\Models\Ticket $ticket
     * @return mixed
     */
    public function update(User $user, Ticket $ticket)
    {
        return (new Authorization($user))->userHasPermissionTo('update', 'ticket', $ticket->id, true, request('group_type'), request('group_id'));
    }

    /**
     * Determine whether the user can delete the app models ticket.
     *
     * @param  \App\Base\Models\User             $user
     * @param  \App\Ticket\Models\Ticket $ticket
     * @return mixed
     */
    public function delete(User $user, Ticket $ticket)
    {
        return (new Authorization($user))->userHasPermissionTo('delete', 'ticket', $ticket->id, true, request('group_type'), request('group_id'));
    }
}
