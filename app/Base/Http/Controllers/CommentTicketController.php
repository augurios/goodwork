<?php

namespace App\Base\Http\Controllers;

use App\Base\Models\CommentTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Base\Utilities\GroupTrait;
use App\Base\Repositories\CommentTicketRepository;
use App\Base\Repositories\MentionRepository;
use App\Base\Http\Requests\ValidateCommentTicketCreation;

class CommentTicketController extends Controller
{
    use GroupTrait;

    /**
     * @var CommentTicketRepository
     */
    private $repository;

    /**
     * @param CommentTicketRepository $repository
     */
    public function __construct(CommentTicketRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request      $request
     * @return JsonResponse
     */
    public function store(ValidateCommentTicketCreation $request, MentionRepository $mentionRepository): JsonResponse
    {
        try {
            $comment = $this->repository->create($request->all());
            if (request('mentions')) {
                $mentionRepository->create('comment', $comment);
            }
            $comment->load('user:id,name,avatar');

            return $this->successResponse(
                'misc.Comment Ticket has been saved',
                'comment',
                $comment,
                201
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function index()
    {
        // $group = $this->getGroupModel();
        // if ($group->notOpenForPublic()) {
        //     abort(401);
        // } elseif (auth()->user()) {
        //     $this->authorize('view', $group);
        // }
        $comments = $this->repository->getAllCommentTicketsWithUser(request('commentable_type'), (int) request('commentable_id'));

        return $this->successResponse(null, 'comments', $comments);
    }

    public function delete(CommentTicket $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json([
            'status'  => 'success',
            'message' => localize('misc.Comment Ticket has been deleted'),
        ]);
    }
}
