<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
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

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
            'sumberClientOptions' => Client::SOURCE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('clients.create', [
            'client' => new Client(),
            'sumberClientOptions' => Client::SOURCE_OPTIONS,
            'businessTypeOptions' => Client::BUSINESS_TYPE_OPTIONS,
            'customBusinessTypeValue' => Client::CUSTOM_BUSINESS_TYPE,
        ]);
    }

    public function show(Client $client): View
    {
        $client->load([
            'quotations' => fn ($query) => $query->latest('tanggal_penawaran')->latest('id'),
        ]);

        return view('clients.show', [
            'client' => $client,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->clientPayload());

        return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan.');
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', [
            'client' => $client,
            'sumberClientOptions' => Client::SOURCE_OPTIONS,
            'businessTypeOptions' => Client::BUSINESS_TYPE_OPTIONS,
            'customBusinessTypeValue' => Client::CUSTOM_BUSINESS_TYPE,
        ]);
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->clientPayload());

        return redirect()->route('clients.show', $client)->with('success', 'Client berhasil diupdate.');
    }
}
