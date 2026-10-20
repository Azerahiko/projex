<?php

namespace App\Http\Controllers\Task;

use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();

        $tasks = Task::with(['project:id,name', 'assignee:id,name,email'])
            ->whereHas('project', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('users.id', $user->id));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('project'), fn ($query) => $query->where('project_id', $request->integer('project')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('tasks/index', [
            'tasks' => $tasks,
            'filters' => $request->only(['status', 'project']),
            'projects' => $this->accessibleProjects($user),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): Response
    {
        $user = auth()->user();
        $projects = $this->accessibleProjects($user);
        $selectedProjectId = $request->integer('project') ?: null;

        return Inertia::render('tasks/create', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->authorize('view-project', $data['project_id']);

        $data['status'] = $data['status'] ?? 'todo';
        $data['priority'] = $data['priority'] ?? 'sedang';

        $task = Task::create($data);

        $this->activityLogger->log(
            ActivityType::TaskCreated,
            "Membuat tugas \"{$task->title}\"",
            auth()->user(),
            $task->project,
            $task,
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): Response
    {
        $this->authorize('view-project', $task->project_id);

        $task->load([
            'project:id,name',
            'assignee:id,name,email',
            'comments.author:id,name,email',
        ]);

        $activities = $task->activities()
            ->with(['user:id,name', 'task:id,title'])
            ->latest()
            ->limit(30)
            ->get();

        return Inertia::render('tasks/show', [
            'task' => $task,
            'comments' => $task->comments,
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task): Response
    {
        $this->authorize('view-project', $task->project_id);

        $task->load(['project:id,name', 'assignee:id,name,email']);

        return Inertia::render('tasks/edit', [
            'task' => $task,
            'projects' => $this->accessibleProjects(auth()->user()),
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('view-project', $task->project_id);

        $data = $request->validated();
        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_to;

        $this->authorize('view-project', $data['project_id']);

        $task->update($data);

        // Log status change
        if ($oldStatus !== $task->status) {
            $this->activityLogger->log(
                ActivityType::TaskStatusChanged,
                "Mengubah status tugas \"{$task->title}\" dari \"{$oldStatus}\" menjadi \"{$task->status}\"",
                auth()->user(),
                $task->project,
                $task,
                ['old_status' => $oldStatus, 'new_status' => $task->status],
            );

            $this->notifyAssignee(
                $task,
                new TaskStatusChangedNotification($task, $oldStatus, $task->status, auth()->user()),
            );
        }

        // Log assignment change
        if ($oldAssignedTo !== $task->assigned_to) {
            $assignee = $task->assignee;
            $assigneeName = $assignee ? $assignee->name : 'tidak ada penanggung jawab';
            $this->activityLogger->log(
                ActivityType::TaskAssigned,
                "Menugaskan tugas \"{$task->title}\" kepada {$assigneeName}",
                auth()->user(),
                $task->project,
                $task,
                ['old_assigned_to' => $oldAssignedTo, 'new_assigned_to' => $task->assigned_to],
            );

            if ($assignee) {
                $this->notifyAssignee(
                    $task,
                    new TaskAssignedNotification($task, auth()->user()),
                );
            }
        }

        // Log general update if not just status/assignment
        if ($oldStatus === $task->status && $oldAssignedTo === $task->assigned_to) {
            $this->activityLogger->log(
                ActivityType::TaskUpdated,
                "Memperbarui tugas \"{$task->title}\"",
                auth()->user(),
                $task->project,
                $task,
            );
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('view-project', $task->project_id);

        $project = $task->project;
        $taskTitle = $task->title;

        $this->activityLogger->log(
            ActivityType::TaskDeleted,
            "Menghapus tugas \"{$taskTitle}\"",
            auth()->user(),
            $project,
            $task,
        );

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Get projects the current user can access.
     *
     * @return Collection<int, Project>
     */
    private function accessibleProjects(User $user): Collection
    {
        return Project::with(['owner:id,name,email', 'members:id,name,email'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('users.id', $user->id));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'user_id']);
    }

    /**
     * Send notification to the task assignee if different from the actor.
     */
    private function notifyAssignee(Task $task, BaseNotification $notification): void
    {
        $assignee = $task->assignee;
        $actor = auth()->user();

        if ($assignee && $assignee->id !== $actor->id) {
            Notification::send($assignee, $notification);
        }
    }
}
