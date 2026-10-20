<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): Response
    {
        return inertia('auth/login');
    }

    /**
     * Handle an incoming login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('Login gagal', ['email' => $credentials['email']]);

            return redirect()->route('login')->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
