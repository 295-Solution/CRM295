<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - ACM 295 Solution</title>

    {{-- ===== SEO & Open Graph (WhatsApp / Social Preview) ===== --}}
    <meta name="title" content="Dashboard - ACM 295 Solution">
    <meta name="description" content="Sistem Access CCTV Monitoring (ACM) internal 295 Solution.">

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type"         content="website">
    <meta property="og:url"          content="{{ url()->current() }}">
    <meta property="og:title"        content="Dashboard - ACM 295 Solution">
    <meta property="og:description"  content="Sistem Access CCTV Monitoring (ACM) internal 295 Solution.">
    <meta property="og:image"        content="{{ asset('images/og-preview.png') }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name"    content="295 Solution">
    <meta property="og:locale"       content="id_ID">

    {{-- Twitter --}}
    <meta property="twitter:card"        content="summary_large_image">
    <meta property="twitter:url"         content="{{ url()->current() }}">
    <meta property="twitter:title"       content="Dashboard - ACM 295 Solution">
    <meta property="twitter:description" content="Sistem Access CCTV Monitoring (ACM) internal 295 Solution." >
    <meta property="twitter:image"       content="{{ asset('images/og-preview.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.sidebar-styles')
    @include('partials.app-styles')
    <style>
        .dashboard-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 960px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .dashboard-grid .span-full {
                grid-column: span 2;
            }
        }
        .list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f5;
        }
        .list-row:last-child { border-bottom: none; }
        .list-row-name { font-weight: 600; font-size: 14px; color: #1e2022; }
        .list-row-sub  { font-size: 12px; color: #9ca3af; margin-top: 2px; }
        .stat-card-border-t { border-top: 3px solid #1a6b74; padding-top: 17px; }
        .stat-card-border-t.green  { border-color: #16a34a; }
        .stat-card-border-t.red    { border-color: #dc2626; }
        .stat-card-border-t.gray   { border-color: #9ca3af; }
    </style>
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <div class="page-wrap">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard CRM</h1>
                    <p class="page-subtitle">Ringkasan data client dan penawaran tim Anda.</p>
                </div>
            </div>

            {{-- KPI Money Row --}}
            <div class="stat-grid" style="grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); margin-bottom:20px;">
                <div class="stat-card">
                    <div class="stat-label">Calon Deal Value</div>
                    <div class="stat-value money">Rp{{ number_format($overview['calon_deal_value'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Deal Value</div>
                    <div class="stat-value money teal">Rp{{ number_format($overview['deal_value'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">HPP Value</div>
                    <div class="stat-value money">Rp{{ number_format($overview['hpp_value'], 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Counter Row --}}
            <div class="stat-grid" style="grid-template-columns: repeat(auto-fit,minmax(140px,1fr)); margin-bottom:28px;">
                <div class="stat-card stat-card-border-t">
                    <div class="stat-label">Total Penawaran</div>
                    <div class="stat-value">{{ number_format($overview['total_penawaran']) }}</div>
                </div>
                <div class="stat-card stat-card-border-t green">
                    <div class="stat-label">Total Deal</div>
                    <div class="stat-value success">{{ number_format($overview['deal_total']) }}</div>
                </div>
                <div class="stat-card stat-card-border-t red">
                    <div class="stat-label">Total Batal</div>
                    <div class="stat-value danger">{{ number_format($overview['batal_total']) }}</div>
                </div>
                <div class="stat-card stat-card-border-t gray">
                    <div class="stat-label">Total Client</div>
                    <div class="stat-value">{{ number_format($totalClients) }}</div>
                </div>
            </div>

            {{-- Analytic sections --}}
            <div class="dashboard-grid">
                {{-- Sumber Client --}}
                <div class="card">
                    <h2 class="section-title">Sumber Client</h2>
                    @if ($sourceCounts->isEmpty())
                        <div class="empty-state">Belum ada data sumber client.</div>
                    @else
                        @foreach ($sourceCounts as $row)
                            <div class="list-row">
                                <span class="list-row-name">{{ ucfirst($row->sumber_client) }}</span>
                                <span class="chip">{{ number_format($row->total) }} Client</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Jenis Projek --}}
                <div class="card">
                    <h2 class="section-title">Jenis Projek</h2>
                    @if ($jenisProjekCounts->isEmpty())
                        <div class="empty-state">Belum ada data jenis projek.</div>
                    @else
                        @foreach ($jenisProjekCounts as $row)
                            <div class="list-row">
                                <span class="list-row-name">{{ ucfirst($row->nama_projek) }}</span>
                                <span class="chip" style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;">{{ number_format($row->total) }} Projek</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Client Terbaru --}}
                <div class="card span-full">
                    <h2 class="section-title">Client Terbaru</h2>
                    @if ($latestClients->isEmpty())
                        <div class="empty-state">Belum ada client yang ditambahkan.</div>
                    @else
                        @foreach ($latestClients as $client)
                            <div class="list-row">
                                <div>
                                    <div class="list-row-name">{{ $client->nama }}</div>
                                    <div class="list-row-sub">{{ $client->perusahaan ?: 'Tanpa perusahaan' }} &nbsp;&bull;&nbsp; {{ $client->nomor_wa }}</div>
                                </div>
                                <span class="chip">{{ ucfirst($client->sumber_client) }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
