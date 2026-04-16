<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\Quotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const LEAD_STATUSES = ['chat_masuk', 'calon_klien', 'klien', 'deal', 'batal'];

    public function index(Request $request): View
    {
        $user = $request->user();
        $selectedYear = (int) $request->query('year', now()->year);
        $filters = [
            'sales_id' => $request->query('sales_id'),
            'status' => $request->query('status'),
            'sumber_lead' => trim((string) $request->query('sumber_lead', '')),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];
        [$fromDate, $toDate] = $this->resolveDateRange($filters, $selectedYear);

        $availableYearsQuery = Lead::query();

        if (! $user->isAdmin()) {
            $availableYearsQuery->where('assigned_to', $user->id);
        }

        $availableYears = $availableYearsQuery
            ->orderByDesc('created_at')
            ->get(['created_at'])
            ->map(fn (Lead $lead): ?int => $lead->created_at?->year)
            ->filter()
            ->unique()
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        if (! $availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->first();
        }

        $filters['from_date'] = $fromDate?->toDateString() ?? '';
        $filters['to_date'] = $toDate?->toDateString() ?? '';

        $monthlyRowsQuery = Quotation::query()
            ->whereYear('updated_at', $selectedYear)
            ->select(['status', 'updated_at']);

        $this->applyDateRangeFilter($monthlyRowsQuery, 'updated_at', $fromDate, $toDate);

        $monthlyRows = $monthlyRowsQuery
            ->get()
            ->groupBy(fn (Quotation $quotation): int => (int) $quotation->updated_at?->month);

        $monthlyClosing = collect(range(1, 12))->map(function (int $month) use ($monthlyRows): array {
            $rows = $monthlyRows->get($month, collect());

            return [
                'month_label' => now()->startOfYear()->addMonths($month - 1)->translatedFormat('M'),
                'deal_total' => $rows->where('status', 'accepted')->count(),
                'batal_total' => $rows->where('status', 'rejected')->count(),
            ];
        });

        $perSalesQuery = Lead::query()
            ->with('assignedUser:id,name')
            ->selectRaw('assigned_to, count(*) as total_leads')
            ->selectRaw("SUM(CASE WHEN status = 'deal' THEN 1 ELSE 0 END) as deal_total")
            ->selectRaw("SUM(CASE WHEN status = 'batal' THEN 1 ELSE 0 END) as batal_total")
            ->whereYear('created_at', $selectedYear);

        $this->applyLeadFilters($perSalesQuery, $filters, $user);
        $this->applyDateRangeFilter($perSalesQuery, 'created_at', $fromDate, $toDate);

        $perSales = $perSalesQuery
            ->groupBy('assigned_to')
            ->orderByDesc('total_leads')
            ->get();

        $perClientQuery = \App\Models\Client::query()
            ->withSum('quotations as total_quotation_value', 'nilai_penawaran')
            ->withCount('quotations')
            ->whereYear('created_at', $selectedYear);

        $this->applyDateRangeFilter($perClientQuery, 'created_at', $fromDate, $toDate);

        $perClient = $perClientQuery
            ->orderByDesc('total_quotation_value')
            ->limit(20)
            ->get(['id', 'nama', 'perusahaan']);

        $overviewLeadsQuery = Lead::query()->whereYear('created_at', $selectedYear);
        $this->applyLeadFilters($overviewLeadsQuery, $filters, $user);
        $this->applyDateRangeFilter($overviewLeadsQuery, 'created_at', $fromDate, $toDate);

        $overviewStatusQuery = Lead::query()->whereYear('updated_at', $selectedYear);
        $this->applyLeadFilters($overviewStatusQuery, $filters, $user);
        $this->applyDateRangeFilter($overviewStatusQuery, 'updated_at', $fromDate, $toDate);

        $quotationOverviewQuery = Quotation::query()
            ->whereYear('created_at', $selectedYear);
        $this->applyDateRangeFilter($quotationOverviewQuery, 'created_at', $fromDate, $toDate);

        if ($user->isAdmin()) {
            $salesOptions = User::query()
                ->whereIn('id', Lead::query()->whereYear('created_at', $selectedYear)->select('assigned_to'))
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $salesOptions = User::query()
                ->where('id', $user->id)
                ->get(['id', 'name']);
            $filters['sales_id'] = (string) $user->id;
        }

        $sumberLeadOptionsQuery = Lead::query()
            ->whereYear('created_at', $selectedYear)
            ->whereNotNull('sumber_lead')
            ->where('sumber_lead', '!=', '');

        if (! $user->isAdmin()) {
            $sumberLeadOptionsQuery->where('assigned_to', $user->id);
        }

        $sumberLeadOptions = $sumberLeadOptionsQuery
            ->select('sumber_lead')
            ->distinct()
            ->orderBy('sumber_lead')
            ->pluck('sumber_lead');

        $funnelTransitions = [];

        $overview = [
            'total_penawaran' => (clone $quotationOverviewQuery)->count(),
            'deal_total' => (clone $quotationOverviewQuery)->where('status', 'accepted')->count(),
            'batal_total' => (clone $quotationOverviewQuery)->where('status', 'rejected')->count(),
            'calon_deal_value' => (clone $quotationOverviewQuery)->whereIn('status', ['pending', 'nego'])->sum('nilai_penawaran'),
            'deal_value' => (clone $quotationOverviewQuery)->where('status', 'accepted')->sum('nilai_penawaran'),
            'hpp_value' => (clone $quotationOverviewQuery)->where('status', 'accepted')->sum('hpp'),
        ];

        $reminderTargetDate = $toDate?->copy()->endOfDay() ?? now()->endOfDay();

        $reminderQuery = Activity::query()
            ->with('lead.assignedUser:id,name,email')
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<', $reminderTargetDate)
            ->whereHas('lead', function (Builder $query) use ($filters, $user): void {
                $this->applyLeadFilters($query, $filters, $user);
            });

        $overdueActivities = $reminderQuery->orderBy('next_follow_up')->get();

        $salesHealth = $overdueActivities
            ->filter(fn (Activity $activity) => $activity->lead?->assignedUser)
            ->groupBy(fn (Activity $activity) => $activity->lead->assignedUser->id)
            ->map(function ($rows): array {
                $sales = $rows->first()->lead->assignedUser;

                return [
                    'sales_name' => $sales->name,
                    'sales_email' => $sales->email,
                    'overdue_count' => $rows->count(),
                    'next_oldest_followup' => optional($rows->first()->next_follow_up)?->format('d M Y H:i'),
                ];
            })
            ->sortByDesc('overdue_count')
            ->values()
            ->take(6);

        $reminderHealth = [
            'target_date' => $reminderTargetDate->toDateString(),
            'total_overdue' => $overdueActivities->count(),
            'unassigned_overdue' => $overdueActivities->filter(fn (Activity $activity) => ! $activity->lead?->assignedUser)->count(),
            'sales' => $salesHealth,
        ];

        $sumberClientQuery = \App\Models\Client::query()
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('sumber_client, count(*) as total')
            ->whereNotNull('sumber_client')
            ->where('sumber_client', '!=', '')
            ->groupBy('sumber_client')
            ->orderByDesc('total');
        $this->applyDateRangeFilter($sumberClientQuery, 'created_at', $fromDate, $toDate);
        $sumberClientCounts = $sumberClientQuery->get();

        $jenisBisnisQuery = \App\Models\Client::query()
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('jenis_bisnis, count(*) as total')
            ->whereNotNull('jenis_bisnis')
            ->where('jenis_bisnis', '!=', '')
            ->groupBy('jenis_bisnis')
            ->orderByDesc('total');
        $this->applyDateRangeFilter($jenisBisnisQuery, 'created_at', $fromDate, $toDate);
        $jenisBisnisCounts = $jenisBisnisQuery->get();

        $jenisProjekQuery = Quotation::query()
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('nama_projek, count(*) as total')
            ->whereNotNull('nama_projek')
            ->where('nama_projek', '!=', '')
            ->groupBy('nama_projek')
            ->orderByDesc('total');
        $this->applyDateRangeFilter($jenisProjekQuery, 'created_at', $fromDate, $toDate);
        $jenisProjekCounts = $jenisProjekQuery->get();

        return view('reports.index', [
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'filters' => $filters,
            'salesOptions' => $salesOptions,
            'sumberLeadOptions' => $sumberLeadOptions,
            'statusOptions' => self::LEAD_STATUSES,
            'funnelTransitions' => $funnelTransitions,
            'monthlyClosing' => $monthlyClosing,
            'perSales' => $perSales,
            'perClient' => $perClient,
            'sumberClientCounts' => $sumberClientCounts,
            'jenisBisnisCounts' => $jenisBisnisCounts,
            'jenisProjekCounts' => $jenisProjekCounts,
            'overview' => $overview,
            'reminderHealth' => $reminderHealth,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $selectedYear = (int) $request->query('year', now()->year);
        $filters = [
            'sales_id' => $request->query('sales_id'),
            'status' => $request->query('status'),
            'sumber_lead' => trim((string) $request->query('sumber_lead', '')),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];
        [$fromDate, $toDate] = $this->resolveDateRange($filters, $selectedYear);

        $rowsQuery = Lead::query()
            ->with('assignedUser:id,name')
            ->whereYear('created_at', $selectedYear);

        $this->applyLeadFilters($rowsQuery, $filters, $user);
        $this->applyDateRangeFilter($rowsQuery, 'created_at', $fromDate, $toDate);

        $rows = $rowsQuery
            ->orderByDesc('created_at')
            ->get(['nama_client', 'perusahaan', 'status', 'sumber_lead', 'created_at', 'assigned_to']);

        $fileName = "crm-report-{$selectedYear}.csv";

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'nama_client',
                'perusahaan',
                'status',
                'sumber_lead',
                'assigned_to',
                'quotation_count',
                'quotation_value',
                'deal_value',
                'hpp_value',
                'created_at',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->nama_client,
                    $row->perusahaan,
                    $row->status,
                    $row->sumber_lead,
                    $row->assignedUser?->name,
                    $row->quotations_count,
                    (float) ($row->quotation_value ?? 0),
                    (float) ($row->deal_value ?? 0),
                    (float) ($row->hpp_value ?? 0),
                    optional($row->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportSalesMonthlyCsv(Request $request): StreamedResponse
    {
        $selectedYear = (int) $request->query('year', now()->year);
        $filters = [
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
        ];
        [$fromDate, $toDate] = $this->resolveDateRange($filters, $selectedYear);

        $query = Quotation::query()
            ->whereYear('created_at', $selectedYear);

        $this->applyDateRangeFilter($query, 'created_at', $fromDate, $toDate);

        $rows = $query->get();

        $grouped = $rows->groupBy(function (Quotation $quotation): string {
            return $quotation->created_at?->format('Y-m') ?? 'unknown';
        })->map(function ($group, string $month): array {
            return [
                'month' => $month,
                'total_penawaran' => $group->count(),
                'total_deal' => $group->where('status', 'accepted')->count(),
                'total_batal' => $group->where('status', 'rejected')->count(),
                'calon_deal_value' => (float) $group->whereIn('status', ['pending', 'nego'])->sum('nilai_penawaran'),
                'deal_value' => (float) $group->where('status', 'accepted')->sum('nilai_penawaran'),
                'hpp_value' => (float) $group->where('status', 'accepted')->sum('hpp'),
            ];
        })->sortBy('month')->values();

        $fileName = "crm-report-monthly-{$selectedYear}.csv";

        return response()->streamDownload(function () use ($grouped): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'month',
                'total_penawaran',
                'total_deal',
                'total_batal',
                'calon_deal_value',
                'deal_value',
                'hpp_value',
            ]);

            foreach ($grouped as $row) {
                fputcsv($handle, [
                    $row['month'],
                    $row['total_penawaran'],
                    $row['total_deal'],
                    $row['total_batal'],
                    $row['calon_deal_value'],
                    $row['deal_value'],
                    $row['hpp_value'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
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

    private function buildFunnelTransitions(int $selectedYear, array $filters, ?Carbon $fromDate, ?Carbon $toDate, $user): array
    {
        $historyQuery = LeadStatusHistory::query()
            ->whereYear('changed_at', $selectedYear)
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
