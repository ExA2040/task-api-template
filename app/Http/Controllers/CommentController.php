<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Services\CommentService;
use App\Http\Requests\StoreCommentRequest; // Import the request
use App\Http\Requests\UpdateCommentRequest; // Import the request
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Task $task)
    {
        return CommentResource::collection($this->commentService->getCommentsForTask($task));
    }

    public function store(StoreCommentRequest $request, Task $task)
    {
        $comment = $this->commentService->createComment($task, [
            'content' => $request->validated()['content'],
            'user_id' => $request->user()->id,
        ]);

        return new CommentResource($comment);
    }

    public function show(Task $task, Comment $comment)
    {
        return new CommentResource($comment->load('user'));
    }

    public function update(UpdateCommentRequest $request, Task $task, Comment $comment)
    {
        $this->authorize('update', $comment);

        $this->commentService->updateComment($comment, $request->validated());

        return new CommentResource($comment->load('user'));
    }

    public function destroy(Task $task, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $this->commentService->deleteComment($comment);

        return response()->json(null, 204);
    }
}
