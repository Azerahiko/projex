<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $accessibleProjectIds = $this->accessibleProjectIds($user);

        $activities = Activity::with(['user:id,name', 'task:id,title', 'project:id,name'])
            ->whereIn('project_id', $accessibleProjectIds)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('activities/index', [
            'activities' => $activities,
        ]);
    }

    /**
     * Get project IDs the user owns or is a member of.
     *
     * @return Collection<int, int>
     */
    private function accessibleProjectIds(User $user): Collection
    {
        return Project::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('members', fn ($q) => $q->where('users.id', $user->id));
        })->pluck('id');
    }
}
