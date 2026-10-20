<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;

class InvitationController extends Controller
{
    /**
     * Display a listing of invitations.
     */
    public function index(): Response
    {
        $invitations = Invitation::with('inviter')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return inertia('admin/invitations/index', [
            'invitations' => $invitations,
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Store a newly created invitation.
     */
    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Check if there's already a pending invitation for this email
        $existingInvitation = Invitation::where('email', $data['email'])
            ->where('status', Invitation::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return redirect()->back()->withErrors([
                'email' => 'Sudah ada undangan aktif untuk email ini. Silakan batalkan atau tunggu kedaluwarsa terlebih dahulu.',
            ]);
        }

        // Check if user with this email already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            return redirect()->back()->withErrors([
                'email' => 'Pengguna dengan email ini sudah terdaftar di sistem.',
            ]);
        }

        $token = bin2hex(random_bytes(32));

        $invitation = Invitation::create([
            'email' => $data['email'],
            'role' => $data['role'],
            'token' => $token,
            'status' => Invitation::STATUS_PENDING,
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        try {
            Mail::to($data['email'])->send(new InvitationMail($invitation));
        } catch (\Throwable $e) {
            // Log error but don't fail the request - admin can resend
            logger()->error('Failed to send invitation email', [
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan berhasil dikirim ke '.$data['email']);
    }

    /**
     * Resend an invitation email.
     */
    public function resend(Invitation $invitation): RedirectResponse
    {
        if ($invitation->status !== Invitation::STATUS_PENDING) {
            return redirect()->back()->withErrors([
                'invitation' => 'Hanya undangan dengan status menunggu yang dapat dikirim ulang.',
            ]);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => Invitation::STATUS_EXPIRED]);

            return redirect()->back()->withErrors([
                'invitation' => 'Undangan sudah kedaluwarsa dan tidak dapat dikirim ulang.',
            ]);
        }

        // Regenerate token for security
        $invitation->update([
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($invitation->email)->send(new InvitationMail($invitation));
        } catch (\Throwable $e) {
            logger()->error('Failed to resend invitation email', [
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'invitation' => 'Gagal mengirim ulang email. Silakan coba lagi.',
            ]);
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan berhasil dikirim ulang ke '.$invitation->email);
    }

    /**
     * Remove the specified invitation from storage.
     */
    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan berhasil dihapus.');
    }
}
