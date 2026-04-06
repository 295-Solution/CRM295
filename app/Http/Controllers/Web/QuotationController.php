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
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quotation::class);

        $user = $request->user();
        $query = Quotation::query()->with('lead:id,nama_client,perusahaan,status,assigned_to');

        if (! $user->isAdmin()) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('nomor_penawaran', 'like', "%{$search}%")
                    ->orWhereHas('lead', function ($leadQuery) use ($search): void {
                        $leadQuery->where('nama_client', 'like', "%{$search}%")
                            ->orWhere('perusahaan', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($status, ['pending', 'nego', 'accepted', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $quotations = $query->latest('tanggal_penawaran')->paginate(15)->withQueryString();

        $summaryQuery = Quotation::query();

        if (! $user->isAdmin()) {
            $summaryQuery->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'pipeline_value' => (clone $summaryQuery)->whereIn('status', ['pending', 'nego'])->sum('nilai_penawaran'),
            'accepted_this_month' => (clone $summaryQuery)->where('status', 'accepted')
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
            'quotation' => $quotation->load('lead:id,nama_client,perusahaan,status,assigned_to'),
        ]);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        $validated = $request->validate([
            'tanggal_penawaran' => ['required', 'date'],
            'nomor_penawaran' => ['nullable', 'string', 'max:255'],
            'nilai_penawaran' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,nego,accepted,rejected'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $quotation->update($validated);
        $this->applyAcceptedStatusTransition($request->user()?->id, $quotation);

        return redirect()
            ->route('leads.show', $quotation->lead_id)
            ->with('success', 'Quotation berhasil diupdate.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize('delete', $quotation);

        $leadId = $quotation->lead_id;
        $quotation->delete();

        return redirect()
            ->route('leads.show', $leadId)
            ->with('success', 'Quotation berhasil dihapus.');
    }

    private function applyAcceptedStatusTransition(?int $actorId, Quotation $quotation): void
    {
        if ($quotation->status !== 'accepted') {
            return;
        }

        $lead = $quotation->lead;

        if ($lead->status === 'Deal') {
            return;
        }

        $fromStatus = $lead->status;
        $lead->update(['status' => 'Deal']);

        $lead->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => 'Deal',
            'changed_by' => $actorId ?? $lead->assigned_to,
            'changed_at' => now(),
            'note' => 'Status lead diubah otomatis dari quotation accepted',
        ]);
    }
}
