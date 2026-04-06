<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\Quotation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);

        $query = Quotation::with('lead');

        if (! $request->user()->isAdmin()) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $request->user()->id));
        }

        $quotations = $query
            ->latest()
            ->get();

        return response()->json($quotations);
    }

    public function store(StoreQuotationRequest $request)
    {
        $this->authorize('create', Quotation::class);

        $lead = Lead::findOrFail($request->validated()['lead_id']);
        $this->authorize('view', $lead);

        if ($lead->status !== 'Hot') {
            return response()->json([
                'message' => 'Quotation hanya dapat dibuat untuk lead dengan status Hot.',
            ], 422);
        }

        $quotation = Quotation::create($request->validated());

        $this->applyAcceptedStatusTransition($request->user()?->id, $quotation);

        return response()->json([
            'message' => 'Penawaran berhasil dibuat',
            'data' => $quotation->load('lead'),
        ], 201);
    }

    public function show(Quotation $quotation)
    {
        $this->authorize('view', $quotation);

        return response()->json(
            $quotation->load('lead')
        );
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        $quotation->update($request->validated());

        $this->applyAcceptedStatusTransition($request->user()?->id, $quotation);

        return response()->json([
            'message' => 'Penawaran berhasil diupdate',
            'data' => $quotation->load('lead'),
        ]);
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorize('delete', $quotation);

        $quotation->delete();

        return response()->json([
            'message' => 'Penawaran berhasil dihapus',
        ]);
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