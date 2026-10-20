<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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

        return match (true) {
            $user->isAdmin() => $this->adminDashboard($user),
            $user->isProjectManager() => $this->pmDashboard($user),
            $user->isClient() => $this->clientDashboard($user),
            default => $this->employeeDashboard($user),
        };
    }

    /**
     * Admin dashboard - organization-wide stats.
     */
    private function adminDashboard(User $user): Response
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'aktif')->count();
        $archivedProjects = Project::where('status', 'arsip')->count();

        $totalTasks = Task::count();
        $taskStats = [
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in-progress')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        $totalUsers = User::count();
        $usersByRole = [
            'admin' => User::where('role', 'admin')->count(),
            'project_manager' => User::where('role', 'project_manager')->count(),
            'employee' => User::where('role', 'employee')->count(),
            'client' => User::where('role', 'client')->count(),
        ];

        return inertia('dashboard', [
            'dashboardType' => 'admin',
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalProjects' => $totalProjects,
            'taskStats' => $taskStats,
            'totalTasks' => $totalTasks,
            'totalUsers' => $totalUsers,
            'usersByRole' => $usersByRole,
            'assignedTasks' => [],
        ]);
    }

    /**
     * Project Manager dashboard - stats for owned projects.
     */
    private function pmDashboard(User $user): Response
    {
        $ownedProjects = Project::where('user_id', $user->id)->get();
        $projectIds = $ownedProjects->pluck('id');

        $totalProjects = $ownedProjects->count();
        $activeProjects = $ownedProjects->where('status', 'aktif')->count();
        $archivedProjects = $ownedProjects->where('status', 'arsip')->count();

        $taskStats = [
            'todo' => Task::whereIn('project_id', $projectIds)->where('status', 'todo')->count(),
            'in_progress' => Task::whereIn('project_id', $projectIds)->where('status', 'in-progress')->count(),
            'done' => Task::whereIn('project_id', $projectIds)->where('status', 'done')->count(),
        ];

        $totalTasks = Task::whereIn('project_id', $projectIds)->count();

        $assignedTasks = Task::whereIn('project_id', $projectIds)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        return inertia('dashboard', [
            'dashboardType' => 'project_manager',
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalProjects' => $totalProjects,
            'taskStats' => $taskStats,
            'totalTasks' => $totalTasks,
            'assignedTasks' => $assignedTasks,
        ]);
    }

    /**
     * Employee dashboard - personal tasks and joined projects.
     */
    private function employeeDashboard(User $user): Response
    {
        $accessibleProjectIds = Project::accessibleTo($user)->pluck('id');

        $totalProjects = $accessibleProjectIds->count();
        $activeProjects = Project::whereIn('id', $accessibleProjectIds)->where('status', 'aktif')->count();
        $archivedProjects = Project::whereIn('id', $accessibleProjectIds)->where('status', 'arsip')->count();

        $taskStats = [
            'todo' => Task::whereIn('project_id', $accessibleProjectIds)->where('status', 'todo')->count(),
            'in_progress' => Task::whereIn('project_id', $accessibleProjectIds)->where('status', 'in-progress')->count(),
            'done' => Task::whereIn('project_id', $accessibleProjectIds)->where('status', 'done')->count(),
        ];

        $assignedTasks = Task::where('assigned_to', $user->id)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        return inertia('dashboard', [
            'dashboardType' => 'employee',
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalProjects' => $totalProjects,
            'taskStats' => $taskStats,
            'assignedTasks' => $assignedTasks,
        ]);
    }

    /**
     * Client dashboard - projects related to their client account.
     */
    private function clientDashboard(User $user): Response
    {
        if (! $user->client_id) {
            return inertia('dashboard', [
                'dashboardType' => 'client',
                'activeProjects' => 0,
                'archivedProjects' => 0,
                'totalProjects' => 0,
                'taskStats' => ['todo' => 0, 'in_progress' => 0, 'done' => 0],
                'assignedTasks' => [],
            ]);
        }

        $clientProjectIds = Project::where('client_id', $user->client_id)->pluck('id');

        $totalProjects = $clientProjectIds->count();
        $activeProjects = Project::whereIn('id', $clientProjectIds)->where('status', 'aktif')->count();
        $archivedProjects = Project::whereIn('id', $clientProjectIds)->where('status', 'arsip')->count();

        $taskStats = [
            'todo' => Task::whereIn('project_id', $clientProjectIds)->where('status', 'todo')->count(),
            'in_progress' => Task::whereIn('project_id', $clientProjectIds)->where('status', 'in-progress')->count(),
            'done' => Task::whereIn('project_id', $clientProjectIds)->where('status', 'done')->count(),
        ];

        return inertia('dashboard', [
            'dashboardType' => 'client',
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalProjects' => $totalProjects,
            'taskStats' => $taskStats,
            'assignedTasks' => [],
        ]);
    }
}
