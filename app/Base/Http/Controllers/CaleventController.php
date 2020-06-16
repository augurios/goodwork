<?php

namespace App\Base\Http\Controllers;

use App\Base\Models\Calevent;
use App\Base\Http\Requests\ValidateCaleventCreation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CaleventController extends Controller
{
    public function index()
    {
        $event = Calevent::all();
        $event->load('user');
        
        return response()->json([
            'status'     => 'success',
            'events'     => $event,
        ]);
    }

    public function store(ValidateCaleventCreation $request)
    {
        // $this->authorize('create', Link::class);
        function formatDate($date) {
            $newDate = date_create($date);
            return date_format($newDate,'Y-m-d H:i:s');
        };

        try {
            $event = Calevent::create([
                'title'         => $request->title,
                'url'           => $request->url,
                'classes'       => $request->classes, 
                'recurent'      => $request->recurent,
                'startDate'     => formatDate($request->startDate), 
                'endDate'       => formatDate($request->endDate), 
                'posted_by'     => auth()->user()->id,
            ]);

            return $this->successResponse(
                'misc.New Event created',
                'event',
                $event,
                201
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function update(ValidateCaleventCreation $request)
    {
        function formatDate($date) {
            $newDate = date_create($date);
            return date_format($newDate,'Y-m-d H:i:s');
        };
        $event = Calevent::findOrFail($request->id);
        $event->update([
            'title'         => $request->title,
            'url'           => $request->url,
            'classes'       => $request->classes, 
            'recurent'      => $request->recurent, 
            'startDate'     => formatDate($request->startDate), 
            'endDate'       => formatDate($request->endDate), 
        ]);
        
        $event->load('user');

        return response()->json([
            'status'  => 'success',
            'message' => localize('misc.Event has been updated'),
            'event'    => $event
        ]);
    }

    public function delete(Calevent $Event)
    { 
        try {  
            $Event->delete();

            return response()->json([
                'status'  => 'success',
                'message' => localize('misc.The event has been deleted'),
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

  

}
