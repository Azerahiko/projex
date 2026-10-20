<?php

namespace App\Http\Controllers\Project;

use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    /**
     * Display a listing of projects.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $projects = Project::with(['client', 'owner'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('projects/index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): Response
    {
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return Inertia::render('projects/create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        $this->activityLogger->log(
            ActivityType::ProjectCreated,
            "Membuat project \"{$project->name}\"",
            auth()->user(),
            $project,
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil dibuat.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): Response
    {
        $this->authorize('view-project', $project->id);

        $project->load(['client', 'owner', 'tasks', 'members']);

        $activities = $project->activities()
            ->with(['user:id,name', 'task:id,title'])
            ->latest()
            ->limit(30)
            ->get();

        return Inertia::render('projects/show', [
            'project' => $project,
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): Response
    {
        $this->authorize('manage-project', $project->id);

        $project->load('client');
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return Inertia::render('projects/edit', [
            'project' => $project,
            'clients' => $clients,
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('manage-project', $project->id);

        $project->update($request->validated());

        $this->activityLogger->log(
            ActivityType::ProjectUpdated,
            "Memperbarui project \"{$project->name}\"",
            auth()->user(),
            $project,
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil diperbarui.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('manage-project', $project->id);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}
