<?php

namespace App\Ticket\Observers;

use App\Ticket\Models\Ticket;
use App\Base\Utilities\GetRecipientsTrait;
use Illuminate\Support\Facades\Notification;
use App\Ticket\Notifications\TicketCreatedNotification;

class TicketObserver
{
    use GetRecipientsTrait;

    /**
     * Handle the ticket "created" event.
     *
     * @param  \App\Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        Notification::send($this->getRecipients($ticket->ticketable(), $ticket->posted_by), new TicketCreatedNotification($ticket, $ticket->creator));
    }

    /**
     * Handle the ticket "updated" event.
     *
     * @param  \App\Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param  \App\Ticket $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param  \App\Ticket $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param  \App\Ticket $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
    }
}
