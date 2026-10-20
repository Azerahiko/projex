<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\Project::class => \App\Policies\ProjectPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-project', function (User $user, int $projectId): bool {
            return Project::accessibleTo($user)
                ->where('projects.id', $projectId)
                ->exists();
        });

        Gate::define('manage-project', function (User $user, int $projectId): bool {
            return Project::manageableBy($user)
                ->where('projects.id', $projectId)
                ->exists();
        });

        Gate::define('view-task', function (User $user, Task $task): bool {
            return $user->canViewTask($task);
        });

        Gate::define('manage-task', function (User $user, Task $task): bool {
            return $user->canManageTask($task);
        });

        Gate::define('comment-on-task', function (User $user, Task $task): bool {
            return $user->canViewTask($task)
                && ! $user->isClient();
        });
    }
}
