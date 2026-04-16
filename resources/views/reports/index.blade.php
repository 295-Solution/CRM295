<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports - CRM295</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;700;800&display=swap" rel="stylesheet">
    @include('partials.sidebar-styles')
    <style>
        :root {
            --bg: #f3f1eb;
            --panel: #fffdf8;
            --ink: #1f201d;
            --muted: #697065;
            --line: #d9ddce;
            --accent: #1d6f78;
            --deal: #1e9d60;
            --lost: #2f3135;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', 'Manrope', sans-serif;
            color: var(--ink);
            background: #f8f9fa;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px 60px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-family: 'Sora', sans-serif;
            font-size: clamp(28px, 4vw, 36px);
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #1a1d1a;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 16px;
            text-decoration: none;
            color: var(--ink);
            background: #fff;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            cursor: pointer;
        }

        .btn:hover {
            background: #f7f9f6;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: #175e66;
            color: #fff;
            box-shadow: 0 4px 12px rgba(29, 111, 120, 0.2);
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .panel h2 {
            margin: 0 0 20px;
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            color: #2b302b;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-section form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .filter-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            width: 100%;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-field label {
            font-weight: 700;
            color: var(--muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        select, input[type="date"] {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 10px 14px;
            background: #f9fafb;
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            color: #374151;
            transition: all 0.2s ease;
        }

        select:focus, input[type="date"]:focus {
            outline: none;
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(29, 111, 120, 0.1);
        }

        .filter-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding-top: 16px;
            border-top: 1px solid #f1f3f5;
        }

        .kpi {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, 1fr);
        }

        .kpi .item-wide {
            grid-column: span 2;
        }

        @media (max-width: 980px) {
            .kpi {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .kpi {
                grid-template-columns: 1fr;
            }
            .kpi .item-wide {
                grid-column: span 1;
            }
        }

        .kpi .item {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            background: linear-gradient(145deg, #ffffff, #fdfdfd);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi .item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .kpi .icon {
            position: absolute;
            right: 16px;
            top: 20px;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #f3f6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            opacity: 0.8;
        }

        .kpi .label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            font-weight: 700;
            z-index: 1;
        }

        .kpi .value {
            margin-top: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--ink);
            z-index: 1;
            letter-spacing: -0.03em;
        }

        .kpi .value.success { color: var(--deal); }
        .kpi .value.danger { color: #d32f2f; }
        .kpi .value.brand { color: var(--accent); }

        .health-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            margin-bottom: 20px;
        }

        .health-item {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            background: #fafafc;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .health-item .label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            font-weight: 700;
        }

        .health-item .value {
            margin-top: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--ink);
        }

        .severity-pill {
            display: inline-block;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 12px;
            width: max-content;
        }

        .severity-low { background: #e0f2f1; color: #00695c; }
        .severity-medium { background: #fff8e1; color: #ff8f00; }
        .severity-high { background: #ffebee; color: #c62828; }

        .health-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 12px;
        }

        .health-list li {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.2s ease;
        }

        .health-list li:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .health-list li.severity-low { border-left: 6px solid #4db6ac; }
        .health-list li.severity-medium { border-left: 6px solid #ffb300; }
        .health-list li.severity-high { border-left: 6px solid #e53935; }

        .layout {
            display: grid;
            gap: 24px;
            grid-template-columns: 1fr;
        }

        .bars {
            display: grid;
            gap: 12px;
        }

        .bar-item {
            background: #fafafc;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 16px;
        }

        .bar-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .bar-meta strong {
            font-family: 'Sora', sans-serif;
            color: #2b302b;
            font-size: 15px;
        }

        .bar-meta span {
            background: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #e9ecef;
            color: var(--muted);
        }

        .track {
            border-radius: 999px;
            background: #e9ecef;
            height: 12px;
            overflow: hidden;
            display: flex;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }

        .deal-fill { background: linear-gradient(90deg, #1e9d60, #2bd482); }
        .lost-fill { background: linear-gradient(90deg, #3f4247, #5c6068); }

        .table-wrap {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: auto;
            background: #fff;
        }

        table {
            width: 100%;
            min-width: 640px;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f3f5;
            font-size: 14px;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            background: #fafafc;
            font-weight: 700;
        }
        
        tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #f8fbfa;
        }

        .muted { color: var(--muted); font-size: 13px; }

        @media (max-width: 980px) {
            .kpi {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .health-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        @media (min-width: 980px) {
            .layout {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $maxMonth = max(1, $monthlyClosing->map(fn ($item) => $item['deal_total'] + $item['batal_total'])->max());
        $maxIndividualBar = max(1, $monthlyClosing->max(fn ($item) => max($item['deal_total'], $item['batal_total'])));

        $severityLevel = function (int $count): array {
            if ($count >= 8) {
                return ['class' => 'severity-high', 'label' => 'High'];
            }

            if ($count >= 3) {
                return ['class' => 'severity-medium', 'label' => 'Medium'];
            }

            return ['class' => 'severity-low', 'label' => 'Low'];
        };

        $totalSeverity = $severityLevel((int) $reminderHealth['total_overdue']);
        $unassignedSeverity = $severityLevel((int) $reminderHealth['unassigned_overdue']);
    @endphp

    <div class="app-shell">
        @include('partials.sidebar')
        <main class="app-main">
    <div class="container">
        <div class="top">
            <h1>Reporting Center</h1>
        </div>

        <section class="panel filter-section">
            <h2><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> Report Filters</h2>
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="filter-grid">
                    <div class="filter-field">
                        <label for="year">Tahun Report</label>
                        <select id="year" name="year">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="from_date">Dari Tanggal</label>
                        <input id="from_date" type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}">
                    </div>
                    <div class="filter-field">
                        <label for="to_date">Sampai Tanggal</label>
                        <input id="to_date" type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}">
                    </div>
                    <div class="filter-field">
                        <label for="sales_id">Sales (Opsional)</label>
                        <select id="sales_id" name="sales_id">
                            <option value="">Semua Sales</option>
                            @foreach ($salesOptions as $sales)
                                <option value="{{ $sales->id }}" @selected((string) ($filters['sales_id'] ?? '') === (string) $sales->id)>{{ $sales->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="status">Lead Status (Opsional)</label>
                        <select id="status" name="status">
                            <option value="">Semua Status</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="sumber_lead">Sumber Lead (Opsional)</label>
                        <select id="sumber_lead" name="sumber_lead">
                            <option value="">Semua Sumber</option>
                            @foreach ($sumberLeadOptions as $source)
                                <option value="{{ $source }}" @selected(($filters['sumber_lead'] ?? '') === $source)>{{ ucfirst($source) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <div style="display:flex; gap:12px; align-items:center;">
                        <button class="btn btn-primary" type="submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            Terapkan Filter
                        </button>
                        <a class="btn" href="{{ route('reports.index', ['year' => $selectedYear]) }}">Reset</a>
                    </div>
                    <div style="display:flex; gap:12px;">
                        <a class="btn" href="{{ route('reports.export', [
                            'year' => $selectedYear,
                            'sales_id' => $filters['sales_id'] ?? null,
                            'status' => $filters['status'] ?? null,
                            'sumber_lead' => $filters['sumber_lead'] ?? null,
                            'from_date' => $filters['from_date'] ?? null,
                            'to_date' => $filters['to_date'] ?? null,
                        ]) }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Lead CSV
                        </a>
                        <a class="btn" href="{{ route('reports.export.sales-monthly', [
                            'year' => $selectedYear,
                            'sales_id' => $filters['sales_id'] ?? null,
                            'status' => $filters['status'] ?? null,
                            'sumber_lead' => $filters['sumber_lead'] ?? null,
                            'from_date' => $filters['from_date'] ?? null,
                            'to_date' => $filters['to_date'] ?? null,
                        ]) }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Monthly Summary
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <section class="kpi" style="margin-bottom: 24px;">
            <div class="item item-wide">
                <div class="icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div class="label">Total Penawaran</div>
                <div class="value">{{ number_format($overview['total_penawaran']) }}</div>
            </div>
            <div class="item item-wide">
                <div class="icon" style="background:#fff3e0; color:#ef6c00;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="label">Calon Deal Value</div>
                <div class="value">Rp {{ number_format($overview['calon_deal_value'], 0, ',', '.') }}</div>
            </div>
            <div class="item">
                <div class="icon" style="background:#e8f5e9; color:#2e7d32;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="label">Total Deal</div>
                <div class="value success">{{ number_format($overview['deal_total']) }}</div>
            </div>
            <div class="item">
                <div class="icon" style="background:#ffebee; color:#c62828;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
                <div class="label">Total Batal</div>
                <div class="value danger">{{ number_format($overview['batal_total']) }}</div>
            </div>
            <div class="item">
                <div class="icon" style="background:#e8f5e9; color:#2e7d32;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <div class="label">Deal Value</div>
                <div class="value success">Rp {{ number_format($overview['deal_value'], 0, ',', '.') }}</div>
            </div>
            <div class="item">
                <div class="icon" style="background:#f3f6fa; color:#455a64;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <div class="label">Total HPP</div>
                <div class="value">Rp {{ number_format($overview['hpp_value'], 0, ',', '.') }}</div>
            </div>
        </section>

        <section class="panel">
            <h2>Reminder Health</h2>
            <div class="health-grid">
                <div class="health-item">
                    <div class="label">Total Overdue</div>
                    <div class="value">{{ number_format($reminderHealth['total_overdue']) }}</div>
                    <span class="severity-pill {{ $totalSeverity['class'] }}">{{ $totalSeverity['label'] }}</span>
                </div>
                <div class="health-item">
                    <div class="label">Unassigned Overdue</div>
                    <div class="value">{{ number_format($reminderHealth['unassigned_overdue']) }}</div>
                    <span class="severity-pill {{ $unassignedSeverity['class'] }}">{{ $unassignedSeverity['label'] }}</span>
                </div>
                <div class="health-item">
                    <div class="label">Target Date</div>
                    <div class="value" style="font-size: 18px;">{{ \Carbon\Carbon::parse($reminderHealth['target_date'])->format('d M Y') }}</div>
                </div>
            </div>

            @if (collect($reminderHealth['sales'])->isEmpty())
                <p class="muted">Tidak ada overdue follow-up untuk filter aktif.</p>
            @else
                <ul class="health-list">
                    @foreach ($reminderHealth['sales'] as $row)
                        @php
                            $salesSeverity = $severityLevel((int) $row['overdue_count']);
                        @endphp
                        <li class="{{ $salesSeverity['class'] }}">
                            <div>
                                <strong>{{ $row['sales_name'] }}</strong>
                                <div class="muted">Oldest: {{ $row['next_oldest_followup'] ?: '-' }}</div>
                                <span class="severity-pill {{ $salesSeverity['class'] }}">{{ $salesSeverity['label'] }}</span>
                            </div>
                            <div style="text-align:right;">
                                <strong>{{ number_format($row['overdue_count']) }}</strong>
                                <div class="muted">overdue</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="layout">
            <article class="panel" style="grid-column: 1 / -1;">
                <h2 style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #f1f3f5; padding-bottom: 16px; margin-bottom: 24px;">
                    Deal vs Batal per Bulan
                    <div style="display: flex; gap: 16px; font-size: 13px; font-weight: 700; color:var(--muted);">
                        <span style="display:flex; align-items:center; gap:6px;">
                            <span style="width:12px; height:12px; border-radius:4px; background:linear-gradient(0deg, #1e9d60, #2bd482);"></span> Deal
                        </span>
                        <span style="display:flex; align-items:center; gap:6px;">
                            <span style="width:12px; height:12px; border-radius:4px; background:linear-gradient(0deg, #e7513d, #ff715b);"></span> Batal
                        </span>
                    </div>
                </h2>
                
                <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 260px; gap: 8px;">
                    @foreach ($monthlyClosing as $month)
                        @php
                            $dealHeight = ($month['deal_total'] / $maxIndividualBar) * 100;
                            $batalHeight = ($month['batal_total'] / $maxIndividualBar) * 100;
                        @endphp
                        <div style="display: flex; flex-direction: column; align-items: center; flex: 1; height: 100%; justify-content: flex-end; gap: 12px; position: relative;">
                            
                            <!-- Area tooltip saat di-hover -->
                            <div class="chart-col-group" style="display: flex; align-items: flex-end; justify-content: center; width: 100%; gap: 4px; height: 100%; border-radius: 8px; transition: background 0.2s; padding-bottom: 4px;" title="Deal: {{ $month['deal_total'] }} | Batal: {{ $month['batal_total'] }}">
                                
                                <div style="width: 45%; max-width: 32px; min-width: 8px; border-radius: 6px 6px 4px 4px; background: linear-gradient(0deg, #1e9d60, #2bd482); height: {{ $dealHeight }}%; min-height: {{ $month['deal_total'] > 0 ? '4px' : '0' }}; box-shadow: 0 4px 10px rgba(43,212,130,0.2);"></div>
                                
                                <div style="width: 45%; max-width: 32px; min-width: 8px; border-radius: 6px 6px 4px 4px; background: linear-gradient(0deg, #e7513d, #ff715b); height: {{ $batalHeight }}%; min-height: {{ $month['batal_total'] > 0 ? '4px' : '0' }}; box-shadow: 0 4px 10px rgba(255,113,91,0.2);"></div>
                            </div>
                            
                            <div style="font-size: 12px; font-weight: 800; color: var(--muted); text-transform: uppercase;">
                                {{ substr($month['month_label'], 0, 3) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel" style="grid-column: 1 / -1;">
                <h2>Performa per Sales</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Sales</th>
                                <th>Total Leads</th>
                                <th>Deal</th>
                                <th>Batal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($perSales as $row)
                                <tr>
                                    <td>{{ $row->assignedUser?->name ?: 'Unassigned' }}</td>
                                    <td>{{ number_format($row->total_leads) }}</td>
                                    <td>{{ number_format($row->deal_total) }}</td>
                                    <td>{{ number_format($row->batal_total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted">Belum ada data sales untuk tahun ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="panel">
            <h2>Top Client/Prospek by Quotation Value</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Perusahaan</th>
                            <th>Total Quotation</th>
                            <th>Jumlah Quotation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perClient as $client)
                            <tr>
                                <td><a href="{{ route('clients.show', $client) }}" style="color:#1d6f78; font-weight:700; text-decoration:none;">{{ $client->nama }}</a></td>
                                <td>{{ $client->perusahaan ?: '-' }}</td>
                                <td>Rp {{ number_format($client->total_quotation_value ?? 0, 0, ',', '.') }}</td>
                                <td>{{ number_format($client->quotations_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted">Belum ada data client untuk tahun ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>


    </div>
        </main>
    </div>
</body>
</html>
