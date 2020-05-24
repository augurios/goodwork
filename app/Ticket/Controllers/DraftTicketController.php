<?php

namespace App\Ticket\Controllers;

use App\Base\Utilities\GroupTrait;
use App\Ticket\Models\Ticket;
use App\Base\Http\Controllers\Controller;
use App\Ticket\Repositories\TicketRepository;

class DraftTicketController extends Controller
{
    use GroupTrait;

    public function index(TicketRepository $repository)
    {
        // $group = $this->getGroupModel();
        // if ($group->notOpenForPublic()) {
        //     abort(401);
        // } elseif (auth()->user()) {
        //     $this->authorize('view', $group);
        // }
        $drafTickets = $repository->getAllDraftTicketWithCreator(request('group_type'), request('group_id'));

        return response()->json([
            'status'      => 'success',
            'total'       => count($drafTickets),
            'draft_tickets' => $drafTickets,
        ]);
    }

}
