<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest; // Import the request
use App\Http\Requests\UpdateTaskRequest; // Import the request
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\TaskUpdated;
use Illuminate\Support\Facades\Cache;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        // Include project's tasks_last_updated_at in the cache key for invalidation
        $cacheKey = 'tasks_project_'.$project->id.'_user_'.$request->user()->id.'_updated_'.($project->tasks_last_updated_at ? $project->tasks_last_updated_at->timestamp : '0').'_'.md5(serialize($request->query()));

        $tasks = Cache::remember($cacheKey, 60, function () use ($request, $project) {
            return $this->taskService->getTasksForProject($project, $request);
        });

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $task = $this->taskService->createTask($project, $request->validated());

        return new TaskResource($task);
    }

    public function show(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);
        $this->authorize('view', $task);
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);
        $this->authorize('update', $task);

        $oldStatus = $task->status; // Store old status before update

        $this->taskService->updateTask($project, $task, $request->validated());

        // Dispatch notification if status changed or other relevant fields
        if ($oldStatus !== $task->status) {
            $task->project->user->notify(new TaskUpdated($task, $oldStatus, $task->status));
        }

        return new TaskResource($task);
    }

    public function destroy(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($project, $task);

        return response()->json(null, 204);
    }
}
