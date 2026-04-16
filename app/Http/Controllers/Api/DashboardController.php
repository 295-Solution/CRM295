<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
   public function index()
    {
        $totalLeads = Lead::count();

        $chatMasuk = Lead::where('status', 'chat_masuk')->count();
        $calonKlien = Lead::where('status', 'calon_klien')->count();
        $klien = Lead::where('status', 'klien')->count();
        $deal = Lead::where('status', 'deal')->count();
        $batal = Lead::where('status', 'batal')->count();

        $closingThisMonth = Lead::where('status', 'deal')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $pipelineValue = Quotation::whereIn('status', ['berjalan', 'nego'])
            ->sum('nilai_penawaran');

        $perSales = Lead::with('assignedUser')
        ->selectRaw('assigned_to, count(*) as total')
        ->groupBy('assigned_to')
        ->get();

        return response()->json([
            'total_leads' => $totalLeads,

            'status_summary' => [
                'chat_masuk' => $chatMasuk,
                'calon_klien' => $calonKlien,
                'klien' => $klien,
                'deal' => $deal,
                'batal' => $batal,
            ],

            'closing_this_month' => $closingThisMonth,
            'pipeline_value' => $pipelineValue,

            'per_sales' => $perSales,
        ]);
    }
}