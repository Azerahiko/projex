<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        return inertia('settings/index');
    }
}
