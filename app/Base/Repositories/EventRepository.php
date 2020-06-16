<?php

namespace App\Base\Repositories;

use App\Base\Models\Event;

class EventRepository
{
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $event;
    }

    public function getAllEvents()
    {
        return $this->model->get();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function getAllFromPj($type,$entityId,$cycleId)
    {
        return $this->model->where(['eventable_type' => $type, 'eventable_id' => $entityId, 'cycle_id' => $cycleId])->get();
    }
    public function updateEvent($request)
    {
        $event = $this->model->findOrFail($request->id);
        $event->update( [
            'name'         => $request->name,
            'description' => $request->description,
            'time'        => $request->time,
            'place'       => $request->place, 
        ]);
        return $event;
    }
    public function delete($eventId)
    {
        $event = $this->model->findOrFail($eventId);
        $event->delete();

        return $event;
    }
}
