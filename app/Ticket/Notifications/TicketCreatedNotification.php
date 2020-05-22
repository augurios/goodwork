<?php

namespace App\Ticket\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Auth\User;
use App\Ticket\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TicketCreatedNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    /**
     * @var string
     */
    public $groupType;

    /**
     * @var int
     */
    public $groupId;

    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * @var User
     */
    private $creator;

    /**
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->creator = $user;
        $this->groupType = $ticket->ticketable_type;
        $this->groupId = $ticket->ticketable_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via()
    {
        return config('notification.channels');
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed                                          $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage())
            ->from(config('mail.from.address'))
            ->subject('New ticket has opened!')
            ->line('New ticket has been opened - ' . $this->ticket->name)
            ->action('Check it out!', url($this->groupType . 's/' . $this->groupId . '?tool=tickets&id=' . $this->ticket->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'subject'     => $this->creator->only(['id', 'name', 'username', 'avatar']),
            'action'      => 'created new ticket',
            'object_type' => 'ticket',
            'object_name' => $this->ticket->name,
            'object_id'   => $this->ticket->id,
            'url'         => url($this->groupType . 's/' . $this->groupId . '?tool=tickets&id=' . $this->ticket->id),
        ];
    }

    /**
     * @param  mixed $notifiable
     * @return void
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => [
                'subject'     => $this->creator,
                'action'      => 'created new ticket',
                'object_type' => 'ticket',
                'object_name' => $this->ticket->name,
                'object_id'   => $this->ticket->id,
            ],
            'date' => 'Just Now',
        ]);
    }
}
