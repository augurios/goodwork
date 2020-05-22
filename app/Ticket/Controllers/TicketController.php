<?php

namespace App\Ticket\Controllers;

use App\Base\Utilities\GroupTrait;
use App\Ticket\Models\Ticket;
use App\Base\Http\Controllers\Controller;
use App\Ticket\Requests\UpdateTicketRequest;
use App\Ticket\Repositories\TicketRepository;
use App\Ticket\Requests\ValidateTicketCreation;

class TicketController extends Controller
{
    use GroupTrait;

    public function index(TicketRepository $repository)
    {
        
        $tickets = $repository->getAllTicketWithCreator(request('group_type'), request('group_id'));

        return response()->json([
            'status'      => 'success',
            'total'       => count($tickets),
            'tickets' => $tickets,
        ]);
    }

    public function store(ValidateTicketCreation $request, TicketRepository $repository)
    {
        $ticket = $repository->create($request->all());
        $ticket->load(['creator', 'subject']);
        $message = request('draft') ? localize('misc.Your post has been saved') : localize('misc.New ticket post has been created');

        return response()->json([
            'status'     => 'success',
            'message'    => $message,
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * @param  Ticket $ticket
     * @return Ticket
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return response()->json($ticket);
    }

    public function delete(Ticket $ticket)
    {
        // $this->authorize('delete', $ticket);
        $ticket->delete();

        return response()->json([
            'status'  => 'success',
            'message' => localize('misc.The ticket has been deleted'),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, TicketRepository $repository)
    {
        // $this->authorize('update', $ticket);
        $ticket = $repository->update($ticket, $request->all());
        $ticket->assignees()->sync($request->assigned_to);
        $ticket->load(['creator:id,avatar,name,username', 'subject:id,name', 'assignees']);

        return response()->json([
            'status'     => 'success',
            'message'    => localize('misc.The ticket has been updated'),
            'ticket' => $ticket,
        ], 201);
    }
}
