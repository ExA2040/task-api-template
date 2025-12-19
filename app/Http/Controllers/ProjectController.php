<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest; // Import the request
use App\Http\Requests\UpdateProjectRequest; // Import the request
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request)
    {
        return ProjectResource::collection($this->projectService->getProjectsForUser($request->user()));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->createProject($request->user(), $request->validated());

        return new ProjectResource($project);
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);
        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $this->projectService->updateProject($project, $request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return response()->json(null, 204);
    }
}
