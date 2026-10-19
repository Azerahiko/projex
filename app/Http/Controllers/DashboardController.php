<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $ownedProjects = Project::where('user_id', $user->id)
            ->withCount('tasks')
            ->get();

        $memberProjects = $user->projects()
            ->withCount('tasks')
            ->get();

        $allProjects = $ownedProjects->merge($memberProjects)->unique('id');

        $activeProjects = $allProjects->where('status', 'aktif')->count();
        $archivedProjects = $allProjects->where('status', 'arsip')->count();

        $assignedTasks = Task::where('assigned_to', $user->id)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        $taskStats = [
            'todo' => Task::whereHas('project', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id));
            })->where('status', 'todo')->count(),
            'in_progress' => Task::whereHas('project', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id));
            })->where('status', 'in-progress')->count(),
            'done' => Task::whereHas('project', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id));
            })->where('status', 'done')->count(),
        ];

        return inertia('dashboard', [
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalProjects' => $allProjects->count(),
            'assignedTasks' => $assignedTasks,
            'taskStats' => $taskStats,
        ]);
    }
}
