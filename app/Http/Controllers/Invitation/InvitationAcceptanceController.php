<?php

namespace App\Http\Controllers\Invitation;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;

class InvitationAcceptanceController extends Controller
{
    /**
     * Show the invitation acceptance page.
     */
    public function show(string $token): Response
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation) {
            return inertia('invitations/accept', [
                'invitation' => null,
                'error' => 'Token undangan tidak valid atau tidak ditemukan.',
            ]);
        }

        if ($invitation->status === Invitation::STATUS_ACCEPTED) {
            return inertia('invitations/accept', [
                'invitation' => $invitation,
                'error' => 'Undangan ini sudah digunakan dan tidak dapat digunakan kembali.',
            ]);
        }

        if ($invitation->status === Invitation::STATUS_REVOKED) {
            return inertia('invitations/accept', [
                'invitation' => $invitation,
                'error' => 'Undangan ini telah dibatalkan oleh admin.',
            ]);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => Invitation::STATUS_EXPIRED]);

            return inertia('invitations/accept', [
                'invitation' => $invitation,
                'error' => 'Masa berlaku undangan telah habis.',
            ]);
        }

        return inertia('invitations/accept', [
            'invitation' => $invitation,
            'error' => null,
        ]);
    }

    /**
     * Accept the invitation and create/login the user.
     */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation) {
            return redirect()->back()->withErrors([
                'token' => 'Token undangan tidak valid atau tidak ditemukan.',
            ]);
        }

        if (! $invitation->isPending()) {
            $message = match ($invitation->status) {
                Invitation::STATUS_ACCEPTED => 'Undangan ini sudah digunakan dan tidak dapat digunakan kembali.',
                Invitation::STATUS_REVOKED => 'Undangan ini telah dibatalkan oleh admin.',
                Invitation::STATUS_EXPIRED => 'Masa berlaku undangan telah habis.',
                default => 'Undangan tidak valid.',
            };

            return redirect()->back()->withErrors(['token' => $message]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if user with this email already exists
        $user = User::where('email', $invitation->email)->first();

        if ($user) {
            // Link existing user to invitation
            $user->update([
                'role' => $invitation->role,
                'client_id' => $invitation->role === Role::Client->value ? null : $user->client_id,
            ]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'role' => $invitation->role,
            ]);
        }

        // Mark invitation as accepted
        $invitation->update([
            'status' => Invitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Undangan berhasil diterima. Selamat datang di Projex!');
    }
}
