<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class TaskService
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getTasksForProject(Project $project, Request $request): LengthAwarePaginator
    {
        return $this->taskRepository->getTasksForProject($project, $request);
    }

    public function createTask(Project $project, array $data): Task
    {
        $task = $this->taskRepository->create($project, $data);
        $project->update(['tasks_last_updated_at' => Carbon::now()]);
        return $task;
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    public function updateTask(Project $project, Task $task, array $data): bool
    {
        $updated = $this->taskRepository->update($task, $data);
        if ($updated) {
            $project->update(['tasks_last_updated_at' => Carbon::now()]);
        }
        return $updated;
    }

    public function deleteTask(Project $project, Task $task): bool
    {
        $deleted = $this->taskRepository->delete($task);
        if ($deleted) {
            $project->update(['tasks_last_updated_at' => Carbon::now()]);
        }
        return $deleted;
    }
}
