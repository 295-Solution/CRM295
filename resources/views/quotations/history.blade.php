<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History Quotation {{ $quotation->nama_projek }} - CRM295</title>
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
            max-width: 1000px;
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
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #1a1d1a;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 16px;
            text-decoration: none;
            color: #1f201d;
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

        .btn-secondary {
            background: #f9fafb;
            color: #374151;
            border-color: #d1d5db;
        }

        .table-wrap {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: auto;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 640px;
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
            color: #697065;
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

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.03em;
        }
        
        .status-pending { background: #fff0cc; color: #997300; }
        .status-nego { background: #e0f2fe; color: #026aa7; }
        .status-accepted { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #6b7280;
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
                    <h1 class="page-title">History Quotation: {{ $quotation->nama_projek }}</h1>
                    <a class="btn btn-secondary" href="{{ route('quotations.index') }}">Kembali</a>
                </div>

                <div class="panel" style="padding:0; border:none; background:transparent; box-shadow:none;">
                    @if($histories->isEmpty())
                        <div class="panel empty-state">Belum ada history perubahan harga untuk quotation ini.</div>
                    @else
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal / Waktu</th>
                                        <th>Status</th>
                                        <th>Nilai Penawaran</th>
                                        <th>HPP</th>
                                        <th>Diupdate Oleh</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories as $history)
                                        <tr>
                                            <td style="color:#4b5563; font-weight:500;">{{ $history->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower($history->status) }}">
                                                    {{ $history->status }}
                                                </span>
                                            </td>
                                            <td style="font-weight:700;">Rp {{ number_format($history->nilai_penawaran, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($history->hpp, 0, ',', '.') }}</td>
                                            <td>{{ $history->changedBy ? $history->changedBy->name : '-' }}</td>
                                            <td style="color:#6b7280;">{{ $history->catatan ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
