<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\Activity;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::with('lead');

        if (! $request->user()->isAdmin()) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $request->user()->id));
        }

        $activities = $query
            ->latest()
            ->get();

        return response()->json($activities);
    }

    public function store(StoreActivityRequest $request)
    {
        $this->authorize('create', Activity::class);

        $lead = Lead::findOrFail($request->validated()['lead_id']);
        $this->authorize('view', $lead);

        $activity = Activity::create($request->validated());

        return response()->json([
            'message' => 'Activity berhasil dibuat',
            'data' => $activity->load('lead'),
        ], 201);
    }

    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);

        return response()->json(
            $activity->load('lead')
        );
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $activity->update($request->validated());

        return response()->json([
            'message' => 'Activity berhasil diupdate',
            'data' => $activity->load('lead'),
        ]);
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json([
            'message' => 'Activity berhasil dihapus',
        ]);
    }
}