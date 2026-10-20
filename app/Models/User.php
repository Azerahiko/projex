<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $role
 * @property int|null $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 */
#[Fillable(['name', 'email', 'password', 'role', 'client_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Get the projects owned by the user.
     *
     * @return HasMany<Project, $this>
     */
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    /**
     * Get the projects the user is a member of.
     *
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')->withTimestamps();
    }

    /**
     * Get the tasks assigned to the user.
     *
     * @return HasMany<Task, $this>
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get the comments written by the user.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the activities performed by the user.
     *
     * @return HasMany<Activity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the client associated with this user (for Client role).
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the invitations sent by this user.
     *
     * @return HasMany<Invitation, $this>
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(Role|string $role): bool
    {
        if ($role instanceof Role) {
            return $this->role === $role;
        }

        return $this->role->value === $role;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::Admin);
    }

    /**
     * Check if user is a project manager.
     */
    public function isProjectManager(): bool
    {
        return $this->hasRole(Role::ProjectManager);
    }

    /**
     * Check if user is an employee.
     */
    public function isEmployee(): bool
    {
        return $this->hasRole(Role::Employee);
    }

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->hasRole(Role::Client);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user can access any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can manage the given project.
     */
    public function canManageProject(Project $project): bool
    {
        // Admin can manage all projects
        if ($this->isAdmin()) {
            return true;
        }

        // Project owner can manage their projects
        if ($project->user_id === $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view the given project.
     */
    public function canViewProject(Project $project): bool
    {
        // Admin can view all projects
        if ($this->isAdmin()) {
            return true;
        }

        // Project owner can view
        if ($project->user_id === $this->id) {
            return true;
        }

        // Project member can view
        if ($this->projects()->where('projects.id', $project->id)->exists()) {
            return true;
        }

        // Client can view their own projects
        if ($this->isClient() && $this->client_id && $project->client_id === $this->client_id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can manage the given task.
     */
    public function canManageTask(Task $task): bool
    {
        // Admin can manage all tasks
        if ($this->isAdmin()) {
            return true;
        }

        // Project owner can manage all tasks in their projects
        if ($task->project->user_id === $this->id) {
            return true;
        }

        // Task assignee can update their own task
        if ($task->assigned_to === $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view the given task.
     */
    public function canViewTask(Task $task): bool
    {
        return $this->canViewProject($task->project);
    }

    /**
     * Get all accessible project IDs for this user.
     *
     * @return Collection<int, int>
     */
    public function accessibleProjectIds()
    {
        if ($this->isAdmin()) {
            return Project::pluck('id');
        }

        if ($this->isClient() && $this->client_id) {
            return Project::where('client_id', $this->client_id)->pluck('id');
        }

        return Project::where(function ($query) {
            $query->where('user_id', $this->id)
                ->orWhereHas('members', fn ($q) => $q->where('users.id', $this->id));
        })->pluck('id');
    }

    /**
     * Get the projects the user can manage (own projects for PM, all for admin).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Project>
     */
    public function manageableProjects()
    {
        if ($this->isAdmin()) {
            return Project::with('owner')->latest()->get();
        }

        return $this->ownedProjects()->with('owner')->latest()->get();
    }
}
