<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $mode = $request->query('mode', 'due');

        $query = Activity::query()
            ->with('lead:id,nama_client,perusahaan,status,assigned_to')
            ->whereNotNull('next_follow_up');

        if (! $user->isAdmin()) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        if ($mode === 'overdue') {
            $query->where('next_follow_up', '<', now());
        } elseif ($mode === 'today') {
            $query->whereDate('next_follow_up', now()->toDateString());
        } else {
            $query->where('next_follow_up', '<=', now()->endOfDay());
        }

        $tasks = $query->orderBy('next_follow_up')->paginate(20)->withQueryString();

        $countQuery = Activity::query()->whereNotNull('next_follow_up');

        if (! $user->isAdmin()) {
            $countQuery->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        $counts = [
            'due' => (clone $countQuery)->where('next_follow_up', '<=', now()->endOfDay())->count(),
            'overdue' => (clone $countQuery)->where('next_follow_up', '<', now())->count(),
            'today' => (clone $countQuery)->whereDate('next_follow_up', now()->toDateString())->count(),
        ];

        return view('followups.index', [
            'tasks' => $tasks,
            'mode' => $mode,
            'counts' => $counts,
        ]);
    }
}
