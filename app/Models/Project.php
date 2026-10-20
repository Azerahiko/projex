<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $client_id
 * @property string $name
 * @property string|null $description
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\ProjectFactory factory($count = null, $state = [])
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Scope the query to projects the user is allowed to view.
     *
     * @param  Builder<Project>  $query
     */
    public function scopeAccessibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isClient()) {
            return $query->where('projects.client_id', $user->client_id);
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('projects.user_id', $user->id)
                ->orWhereHas('members', fn (Builder $mq) => $mq->where('users.id', $user->id));
        });
    }

    /**
     * Scope the query to projects the user is allowed to manage.
     *
     * @param  Builder<Project>  $query
     */
    public function scopeManageableBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('projects.user_id', $user->id);
    }

    /**
     * Get the user that owns the project.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the client for the project.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the tasks for the project.
     *
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the members for the project.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')->withTimestamps();
    }

    /**
     * Get the activities recorded for the project.
     *
     * @return HasMany<Activity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
