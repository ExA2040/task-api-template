<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class TaskRepository
{
    public function getTasksForProject(Project $project, Request $request): LengthAwarePaginator
    {
        $tasks = $project->tasks();

        if ($request->has('status')) {
            $tasks->where('status', $request->status);
        }

        if ($request->has('due_date')) {
            $tasks->where('due_date', $request->due_date);
        }

        if ($request->has('search')) {
            $tasks->where(function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%')
                      ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        return $tasks->paginate(10);
    }

    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function create(Project $project, array $data): Task
    {
        return $project->tasks()->create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
