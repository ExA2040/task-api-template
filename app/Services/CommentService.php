<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Task;
use App\Repositories\CommentRepository;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    protected CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentsForTask(Task $task): Collection
    {
        return $this->commentRepository->getCommentsForTask($task);
    }

    public function createComment(Task $task, array $data): Comment
    {
        return $this->commentRepository->create($task, $data);
    }

    public function getCommentById(int $id): ?Comment
    {
        return $this->commentRepository->findById($id);
    }

    public function updateComment(Comment $comment, array $data): bool
    {
        return $this->commentRepository->update($comment, $data);
    }

    public function deleteComment(Comment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }
}
