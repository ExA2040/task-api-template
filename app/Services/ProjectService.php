<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    protected ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getProjectsForUser(User $user): Collection
    {
        return $this->projectRepository->getAllForUser($user);
    }

    public function createProject(User $user, array $data): Project
    {
        return $this->projectRepository->create($user, $data);
    }

    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function updateProject(Project $project, array $data): bool
    {
        return $this->projectRepository->update($project, $data);
    }

    public function deleteProject(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
