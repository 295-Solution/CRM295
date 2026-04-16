<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private const LEAD_STATUSES = ['chat_masuk', 'calon_klien', 'klien', 'deal', 'batal'];

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = (int) $request->query('year', now()->year);
        $filters = [
            'sales_id' => $request->query('sales_id'),
            'status' => $request->query('status'),
            'sumber_lead' => trim((string) $request->query('sumber_lead', '')),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];

        [$fromDate, $toDate] = $this->resolveDateRange($filters, $year);

        $overviewLeadsQuery = Lead::query()->whereYear('created_at', $year);
        $this->applyLeadFilters($overviewLeadsQuery, $filters, $user);
        $this->applyDateRangeFilter($overviewLeadsQuery, 'created_at', $fromDate, $toDate);

        $overviewStatusQuery = Lead::query()->whereYear('updated_at', $year);
        $this->applyLeadFilters($overviewStatusQuery, $filters, $user);
        $this->applyDateRangeFilter($overviewStatusQuery, 'updated_at', $fromDate, $toDate);

        $pipelineQuery = Quotation::query()
            ->whereYear('created_at', $year)
            ->whereIn('status', ['berjalan', 'nego'])
            ->whereHas('lead', function (Builder $query) use ($filters, $user): void {
                $this->applyLeadFilters($query, $filters, $user);
            });
        $this->applyDateRangeFilter($pipelineQuery, 'created_at', $fromDate, $toDate);

        $monthlyRows = Lead::query()
            ->whereYear('updated_at', $year)
            ->select(['status', 'updated_at', 'assigned_to', 'sumber_lead'])
            ->tap(fn (Builder $query) => $this->applyLeadFilters($query, $filters, $user))
            ->tap(fn (Builder $query) => $this->applyDateRangeFilter($query, 'updated_at', $fromDate, $toDate))
            ->get()
            ->groupBy(fn (Lead $lead): int => (int) $lead->updated_at?->month);

        $monthlyClosing = collect(range(1, 12))->map(function (int $month) use ($monthlyRows): array {
            $rows = $monthlyRows->get($month, collect());

            return [
                'month' => $month,
                'deal_total' => $rows->where('status', 'deal')->count(),
                'batal_total' => $rows->where('status', 'batal')->count(),
            ];
        })->values();

        return response()->json([
            'filters' => [
                'year' => $year,
                'sales_id' => $filters['sales_id'],
                'status' => $filters['status'],
                'sumber_lead' => $filters['sumber_lead'],
                'from_date' => $fromDate?->toDateString(),
                'to_date' => $toDate?->toDateString(),
            ],
            'overview' => [
                'total_leads' => $overviewLeadsQuery->count(),
                'deal_total' => (clone $overviewStatusQuery)->where('status', 'deal')->count(),
                'batal_total' => (clone $overviewStatusQuery)->where('status', 'batal')->count(),
                'pipeline_value' => $pipelineQuery->sum('nilai_penawaran'),
            ],
            'monthly_closing' => $monthlyClosing,
        ]);
    }

    public function salesMonthly(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = (int) $request->query('year', now()->year);
        $filters = [
            'sales_id' => $request->query('sales_id'),
            'status' => $request->query('status'),
            'sumber_lead' => trim((string) $request->query('sumber_lead', '')),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];
        [$fromDate, $toDate] = $this->resolveDateRange($filters, $year);

        $query = Lead::query()
            ->with('assignedUser:id,name')
            ->with('quotations:id,lead_id,status,nilai_penawaran,hpp')
            ->whereYear('created_at', $year);

        $this->applyLeadFilters($query, $filters, $user);
        $this->applyDateRangeFilter($query, 'created_at', $fromDate, $toDate);

        $rows = $query->get(['id', 'assigned_to', 'status', 'created_at']);

        $grouped = $rows->groupBy(function (Lead $lead): string {
            $month = $lead->created_at?->format('Y-m') ?? 'unknown';
            $sales = $lead->assignedUser?->name ?? 'Unassigned';

            return $month.'|'.$sales;
        })->map(function ($group, string $key): array {
            [$month, $sales] = explode('|', $key);

            $quotationValue = $group->sum(fn (Lead $lead): float => (float) $lead->quotations->sum('nilai_penawaran'));
            $dealValue = $group->sum(fn (Lead $lead): float => (float) $lead->quotations->where('status', 'deal')->sum('nilai_penawaran'));
            $hppValue = $group->sum(fn (Lead $lead): float => (float) $lead->quotations->where('status', 'deal')->sum('hpp'));

            return [
                'month' => $month,
                'sales' => $sales,
                'total_leads' => $group->count(),
                'deal_total' => $group->where('status', 'deal')->count(),
                'batal_total' => $group->where('status', 'batal')->count(),
                'quotation_value' => $quotationValue,
                'deal_value' => $dealValue,
                'hpp_value' => $hppValue,
            ];
        })->sortBy(['month', 'sales'])->values();

        return response()->json([
            'filters' => [
                'year' => $year,
                'sales_id' => $filters['sales_id'],
                'status' => $filters['status'],
                'sumber_lead' => $filters['sumber_lead'],
                'from_date' => $fromDate?->toDateString(),
                'to_date' => $toDate?->toDateString(),
            ],
            'data' => $grouped,
        ]);
    }

    public function funnelConversion(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = (int) $request->query('year', now()->year);
        $filters = [
            'sales_id' => $request->query('sales_id'),
            'status' => $request->query('status'),
            'sumber_lead' => trim((string) $request->query('sumber_lead', '')),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];
        [$fromDate, $toDate] = $this->resolveDateRange($filters, $year);

        return response()->json([
            'filters' => [
                'year' => $year,
                'sales_id' => $filters['sales_id'],
                'status' => $filters['status'],
                'sumber_lead' => $filters['sumber_lead'],
                'from_date' => $fromDate?->toDateString(),
                'to_date' => $toDate?->toDateString(),
            ],
            'transitions' => $this->buildFunnelTransitions($year, $filters, $fromDate, $toDate, $user),
        ]);
    }

    public function followupsHealth(Request $request): JsonResponse
    {
        $user = $request->user();
        $targetDate = $request->query('date')
            ? Carbon::parse((string) $request->query('date'))->endOfDay()
            : now()->endOfDay();

        $salesId = $request->query('sales_id');

        $query = Activity::query()
            ->with('lead.assignedUser:id,name,email')
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<', $targetDate);

        if (! empty($salesId)) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $salesId));
        }

        if (! $user->isAdmin()) {
            $query->whereHas('lead', fn (Builder $leadQuery) => $leadQuery->where('assigned_to', $user->id));
        }

        $activities = $query
            ->orderBy('next_follow_up')
            ->get();

        $grouped = $activities
            ->filter(fn (Activity $activity) => $activity->lead?->assignedUser)
            ->groupBy(fn (Activity $activity) => $activity->lead->assignedUser->id)
            ->map(function ($rows): array {
                $sales = $rows->first()->lead->assignedUser;

                return [
                    'sales_id' => $sales->id,
                    'sales_name' => $sales->name,
                    'sales_email' => $sales->email,
                    'overdue_count' => $rows->count(),
                    'next_oldest_followup' => optional($rows->first()->next_follow_up)?->toIso8601String(),
                    'next_latest_followup' => optional($rows->last()->next_follow_up)?->toIso8601String(),
                    'sample_leads' => $rows->take(5)->map(function (Activity $activity): array {
                        return [
                            'lead_id' => $activity->lead_id,
                            'nama_client' => $activity->lead?->nama_client,
                            'jenis' => $activity->jenis,
                            'next_follow_up' => optional($activity->next_follow_up)?->toIso8601String(),
                        ];
                    })->values(),
                ];
            })
            ->values();

        $unassignedCount = $activities->filter(fn (Activity $activity) => ! $activity->lead?->assignedUser)->count();

        return response()->json([
            'target_date' => $targetDate->toDateString(),
            'total_overdue' => $activities->count(),
            'unassigned_overdue' => $unassignedCount,
            'sales' => $grouped,
        ]);
    }

    private function applyLeadFilters(Builder $query, array $filters, $user): Builder
    {
        if (! $user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        if (! empty($filters['sales_id'])) {
            $query->where('assigned_to', $filters['sales_id']);
        }

        if (in_array($filters['status'] ?? null, self::LEAD_STATUSES, true)) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['sumber_lead'])) {
            $query->where('sumber_lead', $filters['sumber_lead']);
        }

        return $query;
    }

    private function resolveDateRange(array $filters, int $selectedYear): array
    {
        $fromDate = null;
        $toDate = null;

        if (! empty($filters['from_date'])) {
            try {
                $fromDate = Carbon::parse($filters['from_date'])->startOfDay();
            } catch (\Throwable) {
                $fromDate = null;
            }
        }

        if (! empty($filters['to_date'])) {
            try {
                $toDate = Carbon::parse($filters['to_date'])->endOfDay();
            } catch (\Throwable) {
                $toDate = null;
            }
        }

        if ($fromDate && $toDate && $fromDate->greaterThan($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        if (! $fromDate && ! $toDate) {
            $fromDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
            $toDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
        }

        return [$fromDate, $toDate];
    }

    private function applyDateRangeFilter(Builder $query, string $column, ?Carbon $fromDate, ?Carbon $toDate): Builder
    {
        if ($fromDate) {
            $query->where($column, '>=', $fromDate);
        }

        if ($toDate) {
            $query->where($column, '<=', $toDate);
        }

        return $query;
    }

    private function buildFunnelTransitions(int $year, array $filters, ?Carbon $fromDate, ?Carbon $toDate, $user): array
    {
        $historyQuery = LeadStatusHistory::query()
            ->whereYear('changed_at', $year)
            ->whereHas('lead', function (Builder $query) use ($filters, $user): void {
                $this->applyLeadFilters($query, $filters, $user);
            });

        $this->applyDateRangeFilter($historyQuery, 'changed_at', $fromDate, $toDate);

        $histories = $historyQuery->get(['from_status', 'to_status']);

        $transitionCount = $histories->groupBy(function (LeadStatusHistory $history): string {
            return ($history->from_status ?? 'null').'->'.$history->to_status;
        })->map->count();

        $chatCalon = (int) ($transitionCount['chat_masuk->calon_klien'] ?? 0);
        $chatBatal = (int) ($transitionCount['chat_masuk->batal'] ?? 0);
        $calonKlien = (int) ($transitionCount['calon_klien->klien'] ?? 0);
        $calonBatal = (int) ($transitionCount['calon_klien->batal'] ?? 0);
        $klienDeal = (int) ($transitionCount['klien->deal'] ?? 0);
        $klienBatal = (int) ($transitionCount['klien->batal'] ?? 0);

        $rate = fn (int $success, int $drop): float => round(($success / max(1, $success + $drop)) * 100, 1);

        return [
            [
                'label' => 'Chat Masuk ke Calon Klien',
                'success' => $chatCalon,
                'drop' => $chatBatal,
                'rate' => $rate($chatCalon, $chatBatal),
            ],
            [
                'label' => 'Calon Klien ke Klien',
                'success' => $calonKlien,
                'drop' => $calonBatal,
                'rate' => $rate($calonKlien, $calonBatal),
            ],
            [
                'label' => 'Klien ke Deal',
                'success' => $klienDeal,
                'drop' => $klienBatal,
                'rate' => $rate($klienDeal, $klienBatal),
            ],
        ];
    }
}
