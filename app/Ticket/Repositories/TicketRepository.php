<?php

namespace App\Ticket\Repositories;

use App\Ticket\Models\Ticket;
use App\Base\Models\CommentTicket;
class TicketRepository
{
    protected $model;

    public function __construct(Ticket $ticket)
    {
        $this->model = $ticket;
    }

    public function create($data)
    {
        $ticket = $this->model->create([
            'name'                => $data['name'],
            'content'             => $data['content'],
            'subject_id'          => $data['subject_id'],
            'raw_content'         => $data['raw_content'],
            'draft'               => $data['draft'],
            'posted_by'           => auth()->user()->id,
            'discussionable_type' => $data['group_type'],
            'discussionable_id'   => $data['group_id'],
        ]);

        $ticket->assignees()->attach($data['assigned_to']);
        $ticket->load('assignees');

        return $ticket;
    }

    public function getAllTicketWithCreator($type, $entityId)
    {   
        $query;

        if(auth()->user()->role_id == 1) {
            $query = $this->model->where(['discussionable_type' => $type, 'discussionable_id' => $entityId])->with(['creator:id,avatar,name,username', 'subject:id,name'])->get(['id', 'name', 'archived', 'draft', 'content', 'raw_content', 'posted_by', 'created_at', 'subject_id']);
        } else {
            $userId = auth()->user()->id;
            $query = $this->model->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with(['creator:id,avatar,name,username', 'subject:id,name'])->get(['id', 'name', 'archived', 'draft', 'content', 'raw_content', 'posted_by', 'created_at', 'subject_id']);
        }
        
        
        $query->load('assignees');
         foreach ($query as $ticket){ 
            $ticket['comments'] = count(CommentTicket::where(['commentable_type' => 'discussion', 'commentable_id' => $ticket->id])->get('id'));
        } 

        return $query;
    }

    public function getAllDraftTicketWithCreator($type, $entityId)
    {
        $query = $this->model->where(['discussionable_type' => $type, 'discussionable_id' => $entityId, 'posted_by' => auth()->user()->id])->with(['creator:id,avatar,name,username', 'subject:id,name'])->get(['id', 'name', 'archived', 'draft', 'content', 'raw_content', 'posted_by', 'created_at', 'subject_id']);
        foreach ($query as $ticket){ 
            $ticket['comments'] = count(CommentTicket::where(['commentable_type' => 'discussion', 'commentable_id' => $ticket->id])->get('id'));
        }
        $query->load('assignees');
        return $query;
    }

    public function update(Ticket $ticket, $data)
    {
        $ticket->update($data);

        return $ticket;
    }
}
