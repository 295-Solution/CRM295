<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $user = $request->user();
        $leadsQuery = Lead::query()->with('assignedUser:id,name');

        if (! $user->isAdmin()) {
            $leadsQuery->where('assigned_to', $user->id);
        }

        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $assignedTo = $request->query('assigned_to');
        $sort = $request->query('sort', 'latest');

        if ($search !== '') {
            $leadsQuery->where(function ($query) use ($search): void {
                $query->where('nama_client', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['Cold', 'Warm', 'Hot', 'Deal', 'Lost'], true)) {
            $leadsQuery->where('status', $status);
        }

        if ($assignedTo !== null && $assignedTo !== '') {
            $leadsQuery->where('assigned_to', $assignedTo);
        }

        match ($sort) {
            'oldest' => $leadsQuery->oldest(),
            'name' => $leadsQuery->orderBy('nama_client'),
            default => $leadsQuery->latest(),
        };

        $leads = $leadsQuery->paginate(12)->withQueryString();

        $statusCounts = Lead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('leads.index', [
            'leads' => $leads,
            'users' => $users,
            'statusCounts' => $statusCounts,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'assigned_to' => $assignedTo,
                'sort' => $sort,
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Lead::class);

        return view('leads.form', [
            'lead' => new Lead(['status' => 'Cold']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'formAction' => route('leads.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Tambah Lead Baru',
            'submitLabel' => 'Simpan Lead',
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $validated = $request->validated();

        if (! $request->user()->isAdmin()) {
            $validated['assigned_to'] = $request->user()->id;
        }

        $lead = Lead::create($validated);

        $lead->statusHistories()->create([
            'from_status' => null,
            'to_status' => $lead->status,
            'changed_by' => $request->user()?->id ?? $lead->assigned_to,
            'changed_at' => now(),
            'note' => 'Status awal lead dibuat',
        ]);

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', 'Lead berhasil dibuat.');
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load([
            'assignedUser:id,name',
            'statusHistories' => fn ($query) => $query->with('changedByUser:id,name')->latest('changed_at'),
            'activities' => fn ($query) => $query->latest('tanggal'),
            'quotations' => fn ($query) => $query->latest('tanggal_penawaran'),
        ]);

        return view('leads.show', [
            'lead' => $lead,
        ]);
    }

    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);

        return view('leads.form', [
            'lead' => $lead,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'formAction' => route('leads.update', $lead),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Lead',
            'submitLabel' => 'Update Lead',
        ]);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $oldStatus = $lead->status;
        $validated = $request->validated();

        if (! $request->user()->isAdmin()) {
            $validated['assigned_to'] = $lead->assigned_to;
        }

        $lead->update($validated);

        if ($oldStatus !== $lead->status) {
            $lead->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $lead->status,
                'changed_by' => $request->user()?->id ?? $lead->assigned_to,
                'changed_at' => now(),
                'note' => 'Status lead diubah lewat form edit',
            ]);
        }

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', 'Lead berhasil diupdate.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'status' => ['required', 'in:Cold,Warm,Hot,Deal,Lost'],
            'note' => ['nullable', 'string'],
        ]);

        if ($lead->status === $validated['status']) {
            return back()->with('success', 'Status lead tidak berubah.');
        }

        $fromStatus = $lead->status;
        $lead->update(['status' => $validated['status']]);

        $lead->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $lead->status,
            'changed_by' => $request->user()?->id ?? $lead->assigned_to,
            'changed_at' => now(),
            'note' => $validated['note'] ?: 'Status lead diubah dari detail lead',
        ]);

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }

    public function storeActivity(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'string', 'max:100'],
            'catatan' => ['required', 'string'],
            'next_follow_up' => ['nullable', 'date'],
        ]);

        $lead->activities()->create($validated);

        return back()->with('success', 'Activity berhasil ditambahkan.');
    }

    public function storeQuotation(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        if ($lead->status !== 'Hot') {
            return back()->withErrors([
                'quotation' => 'Quotation hanya bisa ditambahkan saat status lead Hot.',
            ]);
        }

        $validated = $request->validate([
            'tanggal_penawaran' => ['required', 'date'],
            'nomor_penawaran' => ['nullable', 'string', 'max:255'],
            'nilai_penawaran' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,nego,accepted,rejected'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $quotation = $lead->quotations()->create($validated);

        if ($quotation->status === 'accepted' && $lead->status !== 'Deal') {
            $fromStatus = $lead->status;
            $lead->update(['status' => 'Deal']);

            $lead->statusHistories()->create([
                'from_status' => $fromStatus,
                'to_status' => 'Deal',
                'changed_by' => $request->user()?->id ?? $lead->assigned_to,
                'changed_at' => now(),
                'note' => 'Status lead diubah otomatis dari quotation accepted',
            ]);
        }

        return back()->with('success', 'Quotation berhasil ditambahkan.');
    }
}
