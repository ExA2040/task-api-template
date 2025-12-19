<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function getAllForUser(User $user): Collection
    {
        return $user->projects()->get();
    }

    public function findById(int $id): ?Project
    {
        return Project::find($id);
    }

    public function create(User $user, array $data): Project
    {
        return $user->projects()->create($data);
    }

    public function update(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }
}
