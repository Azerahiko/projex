<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(): Response
    {
        $clients = Client::withCount('projects')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return inertia('admin/clients/index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return redirect()->route('admin.clients.index')
            ->with('success', 'Klien berhasil ditambahkan.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        // Prevent deleting client that has projects
        if ($client->projects()->exists()) {
            return redirect()->back()->withErrors([
                'client' => 'Tidak dapat menghapus klien yang masih memiliki project. Hapus atau pindahkan project terlebih dahulu.',
            ]);
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Klien berhasil dihapus.');
    }
}
