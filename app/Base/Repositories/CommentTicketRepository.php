<?php

namespace App\Base\Repositories;

use App\Base\Models\CommentTicket;
use Illuminate\Database\Eloquent\Collection;

class CommentTicketRepository
{
    /**
     * @var CommentTicket
     */
    private $model;

    /**
     * @param CommentTicket $comment
     */
    public function __construct(CommentTicket $comment)
    {
        $this->model = $comment;
    }

    /**
     * @param  array         $data
     * @return CommentTicket|Model
     */
    public function create(array $data): CommentTicket
    {
        return $this->model->query()->create([
            'user_id'          => auth()->user()->id,
            'commentable_type' => $data['commentable_type'],
            'commentable_id'   => $data['commentable_id'],
            'body'             => $data['body'],
        ]);
    }

    /**
     * @param string $commentableType
     * @param int    $commentableId
     *
     * @return Collection
     */
    public function getAllCommentTicketsWithUser(string $commentableType, int $commentableId): Collection
    {
        return $this->model
            ->where(['commentable_type' => $commentableType, 'commentable_id' => $commentableId])
            ->with('user:id,name,avatar')
            ->get();
    }
}
