<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return inertia('projects/index');
    }
}
