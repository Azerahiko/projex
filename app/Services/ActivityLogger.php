<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class ActivityLogger
{
    /**
     * Record an activity entry.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public function log(
        ActivityType $type,
        string $description,
        ?User $user = null,
        ?Project $project = null,
        ?Task $task = null,
        ?array $properties = null,
    ): Activity {
        return Activity::create([
            'project_id' => $project->id ?? $task?->project_id,
            'task_id' => $task?->id,
            'user_id' => $user?->id,
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
