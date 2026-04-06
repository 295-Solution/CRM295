<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $query = Lead::with('assignedUser');

        if (! $request->user()->isAdmin()) {
            $query->where('assigned_to', $request->user()->id);
        }

        $leads = $query
            ->latest()
            ->get();

        return response()->json($leads);
    }

    public function store(StoreLeadRequest $request)
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

        return response()->json([
            'message' => 'Lead berhasil dibuat',
            'data' => $lead->load('assignedUser', 'statusHistories'),
        ], 201);
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load([
            'assignedUser',
            'activities',
            'quotations',
            'statusHistories',
        ]);

        return response()->json($lead);
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
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
                'note' => 'Status lead diubah',
            ]);
        }

        return response()->json([
            'message' => 'Lead berhasil diupdate',
            'data' => $lead->load('assignedUser', 'statusHistories'),
        ]);
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return response()->json([
            'message' => 'Lead berhasil dihapus',
        ]);
    }
}