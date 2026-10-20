<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
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

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): Response
    {
        $this->authorize('view-project', $task->project_id);

        $task->load(['project:id,name', 'assignee:id,name,email']);

        return Inertia::render('tasks/show', [
            'task' => $task,
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

        $this->authorize('view-project', $data['project_id']);

        $task->update($data);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('view-project', $task->project_id);

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
}
