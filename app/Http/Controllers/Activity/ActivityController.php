<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * Display a paginated activity feed for projects the user can access.
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();

        $projectIds = Project::accessibleTo($user)->pluck('id');

        $activities = Activity::with(['user:id,name', 'task:id,title', 'project:id,name'])
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('activities/index', [
            'activities' => $activities,
        ]);
    }
}
