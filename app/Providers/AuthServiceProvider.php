<?php

namespace App\Providers;

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

        // Gates for project authorization - will be enabled after Project model is created
        // Gate::define('view-project', function (User $user, int $projectId): bool {
        //     return $user->ownedProjects()->where('id', $projectId)->exists()
        //         || $user->projects()->where('id', $projectId)->exists();
        // });
        //
        // Gate::define('manage-project', function (User $user, int $projectId): bool {
        //     return $user->ownedProjects()->where('id', $projectId)->exists();
        // });
    }
}
