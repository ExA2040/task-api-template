<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository
{
    public function getCommentsForTask(Task $task): Collection
    {
        return $task->comments()->with('user')->get();
    }

    public function findById(int $id): ?Comment
    {
        return Comment::find($id);
    }

    public function create(Task $task, array $data): Comment
    {
        return $task->comments()->create($data);
    }

    public function update(Comment $comment, array $data): bool
    {
        return $comment->update($data);
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}
