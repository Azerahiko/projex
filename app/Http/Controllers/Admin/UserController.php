<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): Response
    {
        $users = User::with('client')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return inertia('admin/users/index', [
            'users' => $users,
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Update the role of a user.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        // Prevent admin from changing their own role
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors([
                'role' => 'Anda tidak dapat mengubah peran Anda sendiri.',
            ]);
        }

        $user->update([
            'role' => $request->validated('role'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Peran pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors([
                'user' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }
}
