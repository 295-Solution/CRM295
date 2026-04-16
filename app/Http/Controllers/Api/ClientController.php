<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        $clients = Client::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('nama', 'like', "%{$search}%")
                        ->orWhere('perusahaan', 'like', "%{$search}%")
                        ->orWhere('nomor_wa', 'like', "%{$search}%")
                        ->orWhere('jenis_bisnis', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return response()->json($clients);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'data' => $client,
        ]);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->clientPayload());

        return response()->json([
            'message' => 'Client berhasil dibuat.',
            'data' => $client,
        ], 201);
    }

    public function update(StoreClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->clientPayload());

        return response()->json([
            'message' => 'Client berhasil diupdate.',
            'data' => $client,
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json([
            'message' => 'Client berhasil dihapus.',
        ]);
    }
}
