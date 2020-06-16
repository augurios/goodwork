<?php

namespace App\Base\Http\Controllers;

use App\Base\Repositories\EventRepository;
use App\Base\Http\Requests\EventStoreRequest;

class EventController extends Controller
{
    private $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $events = $this->repository->getAllEvents();

        return response()->json([
            'status' => 'success',
            'events' => $events,
        ]);
    }

    public function store(EventStoreRequest $request)
    {
        $event = $this->repository->create($request->all());

        return $this->successResponse(
            'misc.New event has been created',
            'event',
            $event,
            201
        );
    }

    public function indexPj(EventRepository $repository)
    {
        $events = $this->repository->getAllFromPj(request('eventable_type'), request('eventable_id'), request('cycle_id'));

        return response()->json([
            'status' => 'success',
            'events' => $events,
        ]);
    }

    public function updateEvent(EventRepository $repository)
    {

        $event = $this->repository->updateEvent(request());

        return response()->json([
            'status'     => 'success',
            'message'    => localize('misc.The event has been updated'),
            'event' => $event,
        ], 201);
    }

    public function delete($EventD)
    { 
        try {  
            $event = $this->repository->delete($EventD);

            return response()->json([
                'status'  => 'success',
                'message' => localize('misc.The event has been deleted'),
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
