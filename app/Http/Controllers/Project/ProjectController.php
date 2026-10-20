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
        $projects = Project::accessibleTo($user)
            ->with(['client', 'owner'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('projects/index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new project.
     * Only Admin and Project Manager can create projects.
     */
    public function create(): Response
    {
        $this->authorizeProjectCreation();

        return Inertia::render('projects/create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorizeProjectCreation();

        $data = $request->validated();
        $client = $this->resolveClientFromData($data);

        $project = Project::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'user_id' => auth()->id(),
            'client_id' => $client?->id,
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

        return Inertia::render('projects/edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('manage-project', $project->id);

        $data = $request->validated();
        $previousClientId = $project->client_id;

        $client = $this->resolveClientFromData($data);

        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'client_id' => $client?->id,
        ]);

        // Clean up orphaned client (no projects, no users)
        if ($previousClientId && $previousClientId !== $client?->id) {
            Client::find($previousClientId)?->deleteIfUnreferenced();
        }

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

    /**
     * Authorize that the user can create projects.
     * Admin, Project Manager, and Employee can create projects.
     */
    private function authorizeProjectCreation(): void
    {
        $user = auth()->user();

        if (! $user->hasAnyPermission(['manage_all_projects', 'manage_owned_projects', 'create_projects'])) {
            abort(403, 'Anda tidak memiliki izin untuk membuat project.');
        }
    }

    /**
     * Resolve client from project form data.
     * Reuses existing client by email to avoid duplication.
     */
    private function resolveClientFromData(array $data): ?Client
    {
        if (blank($data['client_email'] ?? null)) {
            return null;
        }

        $client = Client::firstOrNew(['email' => $data['client_email']]);
        $client->name = $data['client_name'];

        if (filled($data['client_phone'])) {
            $client->phone = $data['client_phone'];
        }
        if (filled($data['client_company'])) {
            $client->company = $data['client_company'];
        }

        $client->save();

        return $client;
    }
}
