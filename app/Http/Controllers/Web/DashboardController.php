<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $leadBaseQuery = Lead::query();
        $activityBaseQuery = Activity::query();
        $quotationBaseQuery = Quotation::query();

        if (! $user->isAdmin()) {
            $leadBaseQuery->where('assigned_to', $user->id);
            $activityBaseQuery->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
            $quotationBaseQuery->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        $statusCounts = [
            'Cold' => (clone $leadBaseQuery)->where('status', 'Cold')->count(),
            'Warm' => (clone $leadBaseQuery)->where('status', 'Warm')->count(),
            'Hot' => (clone $leadBaseQuery)->where('status', 'Hot')->count(),
            'Deal' => (clone $leadBaseQuery)->where('status', 'Deal')->count(),
            'Lost' => (clone $leadBaseQuery)->where('status', 'Lost')->count(),
        ];

        $totalLeads = array_sum($statusCounts);
        $closingThisMonth = (clone $leadBaseQuery)
            ->where('status', 'Deal')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $pipelineValue = (clone $quotationBaseQuery)
            ->whereIn('status', ['pending', 'nego'])
            ->sum('nilai_penawaran');

        $overdueFollowUps = (clone $activityBaseQuery)
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<', now())
            ->count();

        $lostRate = $totalLeads > 0
            ? round(($statusCounts['Lost'] / $totalLeads) * 100, 1)
            : 0;

        $funnel = [
            ['label' => 'Cold', 'value' => $statusCounts['Cold'], 'color' => '#8a8f98'],
            ['label' => 'Warm', 'value' => $statusCounts['Warm'], 'color' => '#f5b100'],
            ['label' => 'Hot', 'value' => $statusCounts['Hot'], 'color' => '#e7513d'],
            ['label' => 'Deal', 'value' => $statusCounts['Deal'], 'color' => '#1e9d60'],
            ['label' => 'Lost', 'value' => $statusCounts['Lost'], 'color' => '#2f3135'],
        ];

        $perSales = (clone $leadBaseQuery)
            ->with('assignedUser:id,name')
            ->selectRaw('assigned_to, count(*) as total')
            ->groupBy('assigned_to')
            ->orderByDesc('total')
            ->get();

        $upcomingFollowUps = (clone $activityBaseQuery)
            ->with('lead:id,nama_client,perusahaan,status')
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '>=', now())
            ->orderBy('next_follow_up')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'totalLeads' => $totalLeads,
            'closingThisMonth' => $closingThisMonth,
            'pipelineValue' => $pipelineValue,
            'lostRate' => $lostRate,
            'overdueFollowUps' => $overdueFollowUps,
            'funnel' => $funnel,
            'perSales' => $perSales,
            'upcomingFollowUps' => $upcomingFollowUps,
        ]);
    }
}