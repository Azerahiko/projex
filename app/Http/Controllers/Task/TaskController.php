<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        return inertia('tasks/index');
    }
}
