<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    private const QUOTATION_STATUSES = ['pending', 'nego', 'accepted', 'rejected'];

    public function create(Request $request): View
    {
        $this->authorize('create', Quotation::class);

        $clientId = $request->query('client_id');
        $clients = \App\Models\Client::orderBy('nama')->get();

        return view('quotations.create', [
            'clients' => $clients,
            'selectedClientId' => $clientId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Quotation::class);

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'nama_projek' => ['required', 'string', 'max:255'],
            'tanggal_penawaran' => ['required', 'date'],
            'nilai_penawaran' => ['required', 'numeric'],
            'hpp' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,nego,accepted,rejected'],
        ]);

        $quotation = new Quotation();
        $quotation->client_id = $validated['client_id'];
        $quotation->nama_projek = $validated['nama_projek'];
        $quotation->tanggal_penawaran = $validated['tanggal_penawaran'];
        $quotation->nilai_penawaran = $validated['nilai_penawaran'];
        $quotation->hpp = $validated['hpp'];
        $quotation->status = $validated['status'];
        $quotation->save();

        $quotation->histories()->create([
            'nilai_penawaran' => $quotation->nilai_penawaran,
            'hpp' => $quotation->hpp,
            'status' => $quotation->status,
            'changed_by' => $request->user()->id,
            'catatan' => 'Quotation dibuat',
        ]);

        return redirect()->route('clients.show', $quotation->client_id)
            ->with('success', 'Quotation berhasil dibuat.');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quotation::class);

        $user = $request->user();
        $query = Quotation::query()->with('client:id,nama,perusahaan');

        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->whereHas('client', function ($clientQuery) use ($search): void {
                    $clientQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('perusahaan', 'like', "%{$search}%");
                });
            });
        }

        if (in_array($status, self::QUOTATION_STATUSES, true)) {
            $query->where('status', $status);
        }

        $quotations = $query->latest('tanggal_penawaran')->paginate(15)->withQueryString();

        $summaryQuery = Quotation::query();

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'quotation_ongoing' => (clone $summaryQuery)->whereIn('status', ['pending', 'nego'])->count(),
            'deal_this_month' => (clone $summaryQuery)->where('status', 'accepted')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
        ];

        return view('quotations.index', [
            'quotations' => $quotations,
            'summary' => $summary,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function edit(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);
        return view('quotations.edit', [
            'quotation' => $quotation,
        ]);
    }

    public function history(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $histories = $quotation->histories()->with('changedBy')->get();

        return view('quotations.history', [
            'quotation' => $quotation,
            'histories' => $histories,
        ]);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        $validated = $request->validate([
            'nama_projek' => ['required', 'string', 'max:255'],
            'tanggal_penawaran' => ['required', 'date'],
            'nilai_penawaran' => ['required', 'numeric'],
            'hpp' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,nego,accepted,rejected'],
        ]);

        $quotation->nama_projek = $validated['nama_projek'];
        $quotation->tanggal_penawaran = $validated['tanggal_penawaran'];
        $quotation->nilai_penawaran = $validated['nilai_penawaran'];
        $quotation->hpp = $validated['hpp'];
        $quotation->status = $validated['status'];
        $isHistoryDirty = $quotation->isDirty(['nilai_penawaran', 'hpp', 'status']);
        $quotation->save();

        if ($isHistoryDirty) {
            $quotation->histories()->create([
                'nilai_penawaran' => $quotation->nilai_penawaran,
                'hpp' => $quotation->hpp,
                'status' => $quotation->status,
                'changed_by' => $request->user()->id,
                'catatan' => 'Quotation diupdate',
            ]);
        }

        return redirect()
            ->route('clients.show', $quotation->client_id)
            ->with('success', 'Quotation berhasil diupdate.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize('delete', $quotation);
        
        $quotation->delete();

        return back()->with('success', 'Quotation berhasil dihapus.');
    }
}
