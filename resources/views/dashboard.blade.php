<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    @include('partials.sidebar-styles')
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', 'Manrope', sans-serif;
            color: #1a1d1a;
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

        .page-title {
            margin: 0;
            font-family: 'Sora', sans-serif;
            font-size: clamp(28px, 4vw, 36px);
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #1a1d1a;
        }
        
        .page-subtitle {
            margin: 8px 0 0;
            color: #697065;
            font-size: 15px;
        }

        .grid {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        
        @media (min-width: 960px) {
            .grid { align-items: start; grid-template-columns: repeat(12, minmax(0, 1fr)); }
            .sources { grid-column: span 12; }
            .latest { grid-column: span 12; }
        }

        .card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .kpi-label {
            color: #697065;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .kpi-value {
            margin-top: 12px;
            font-family: 'Sora', sans-serif;
            font-size: clamp(36px, 5vw, 48px);
            letter-spacing: -0.03em;
            font-weight: 800;
            color: #1a1d1a;
        }

        .title {
            margin: 0 0 16px;
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1a1d1a;
        }

        ul { margin: 0; padding: 0; list-style: none; display: grid; gap: 12px; }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            background: #fdfdfd;
            transition: background 0.15s ease;
        }
        
        .row:hover { background: #f8fbfa; }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            background: #f3f4f6;
        }

        .muted { color: #6b7280; font-size: 13px; margin-top:4px; font-weight: 500; }

        .empty {
            color: #6b7280;
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            background: #f9fafb;
            padding: 24px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <div class="container">
            <div class="top">
                <div>
                    <h1 class="page-title">Dashboard CRM</h1>
                    <p class="page-subtitle">Ringkasan cepat data client dan penawaran harian tim.</p>
                </div>
            </div>
            
            <h2 class="title" style="margin-bottom: 16px;">Ringkasan Penawaran</h2>
            <div style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
                <article class="card" style="padding: 20px;">
                    <div class="kpi-label">Calon Deal Value</div>
                    <div class="kpi-value" style="font-size: clamp(24px, 4vw, 32px);">
                        Rp{{ number_format($overview['calon_deal_value'], 0, ',', '.') }}
                    </div>
                </article>
                <article class="card" style="padding: 20px;">
                    <div class="kpi-label">Deal Value</div>
                    <div class="kpi-value" style="font-size: clamp(24px, 4vw, 32px); color: #0f766e;">
                        Rp{{ number_format($overview['deal_value'], 0, ',', '.') }}
                    </div>
                </article>
                <article class="card" style="padding: 20px;">
                    <div class="kpi-label">HPP Value</div>
                    <div class="kpi-value" style="font-size: clamp(24px, 4vw, 32px);">
                        Rp{{ number_format($overview['hpp_value'], 0, ',', '.') }}
                    </div>
                </article>
            </div>
            
            <div style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 24px;">
                <article class="card" style="padding: 20px; text-align: center; border-bottom: 4px solid #1d6f78;">
                    <div class="kpi-label" style="margin-bottom: 8px;">Total Penawaran</div>
                    <strong style="font-size: 28px; font-family:'Sora',sans-serif;">{{ number_format($overview['total_penawaran']) }}</strong>
                </article>
                <article class="card" style="padding: 20px; text-align: center; border-bottom: 4px solid #16a34a;">
                    <div class="kpi-label" style="margin-bottom: 8px;">Total Deal</div>
                    <strong style="font-size: 28px; font-family:'Sora',sans-serif; color: #16a34a;">{{ number_format($overview['deal_total']) }}</strong>
                </article>
                <article class="card" style="padding: 20px; text-align: center; border-bottom: 4px solid #dc2626;">
                    <div class="kpi-label" style="margin-bottom: 8px;">Total Batal</div>
                    <strong style="font-size: 28px; font-family:'Sora',sans-serif; color: #dc2626;">{{ number_format($overview['batal_total']) }}</strong>
                </article>
                <article class="card" style="padding: 20px; text-align: center; border-bottom: 4px solid #6b7280;">
                    <div class="kpi-label" style="margin-bottom: 8px;">Total Client</div>
                    <strong style="font-size: 28px; font-family:'Sora',sans-serif;">{{ number_format($totalClients) }}</strong>
                </article>
            </div>

            <section class="grid" style="margin-top: 32px;">
                <article class="card sources">
                    <h2 class="title" style="margin-bottom: 20px;">Sumber Client</h2>
                    @if ($sourceCounts->isEmpty())
                        <div class="empty">Belum ada data sumber client.</div>
                    @else
                        <ul style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                            @foreach ($sourceCounts as $row)
                                <li class="row">
                                    <strong style="font-size: 15px;">{{ ucfirst($row->sumber_client) }}</strong>
                                    <span class="chip">{{ number_format($row->total) }} Client</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </article>

                <article class="card latest">
                    <h2 class="title" style="margin-bottom: 20px;">Client Terbaru</h2>
                    @if ($latestClients->isEmpty())
                        <div class="empty">Belum ada client yang ditambahkan.</div>
                    @else
                        <ul>
                            @foreach ($latestClients as $client)
                                <li class="row">
                                    <div>
                                        <strong style="font-size: 16px;">{{ $client->nama }}</strong>
                                        <div class="muted">{{ $client->perusahaan ?: 'Tanpa perusahaan' }} &nbsp;&bull;&nbsp; {{ $client->nomor_wa }}</div>
                                    </div>
                                    <span class="chip" style="background: #e5e7eb;">{{ ucfirst($client->sumber_client) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            </section>
        </div>
    </main>
</div>
</body>
</html>
