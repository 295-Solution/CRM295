<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Client - CRM295</title>
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

        .btn-primary {
            background: #1d6f78;
            color: #fff;
            border-color: #1d6f78;
        }

        .btn-primary:hover {
            background: #175e66;
            color: #fff;
            box-shadow: 0 4px 12px rgba(29, 111, 120, 0.2);
        }
        
        .btn-secondary {
            background: #f9fafb;
            color: #374151;
            border-color: #d1d5db;
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

        .profile-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .meta {
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

        .meta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .meta .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #697065;
            font-weight: 700;
        }

        .meta .value {
            margin-top: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1d1a;
        }

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

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-right: 4px;
        }

        .action-btn:hover {
            color: #1f2937;
            background: #f3f4f6;
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

        .empty {
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            padding: 32px 14px;
            color: #6b7280;
            background: #f9fafb;
            text-align: center;
        }
        
        .section-title {
            margin: 0 0 16px;
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1a1d1a;
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
                    <h1 class="page-title">{{ $client->nama }}</h1>
                    <p class="page-subtitle">{{ $client->perusahaan ?: 'Tanpa perusahaan' }}</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('clients.index') }}">Kembali ke List Client</a>
            </div>

            <section class="panel">
                <h2 class="section-title">Profil Client</h2>
                <div class="profile-grid">
                    <div class="meta">
                        <div class="label">Nama</div>
                        <div class="value">{{ $client->nama }}</div>
                    </div>
                    <div class="meta">
                        <div class="label">Perusahaan</div>
                        <div class="value">{{ $client->perusahaan ?: '-' }}</div>
                    </div>
                    <div class="meta">
                        <div class="label">Nomor WA</div>
                        <div class="value">{{ $client->nomor_wa }}</div>
                    </div>
                    <div class="meta">
                        <div class="label">Sumber Client</div>
                        <div class="value"><span class="chip">{{ ucfirst($client->sumber_client) }}</span></div>
                    </div>
                    <div class="meta">
                        <div class="label">Jenis Bisnis</div>
                        <div class="value">{{ $client->jenis_bisnis ?: '-' }}</div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                    <h2 class="section-title" style="margin:0;">Quotation Client</h2>
                    <a href="{{ route('quotations.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Quotation
                    </a>
                </div>
                
                @if ($client->quotations->isEmpty())
                    <div class="empty">Belum ada quotation untuk client ini.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Nama Projek</th>
                                    <th>Tanggal</th>
                                    <th>Nilai Penawaran</th>
                                    <th>HPP</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client->quotations as $quotation)
                                    <tr>
                                        <td><div style="font-weight:600; color:#1f2937;">{{ $quotation->client->nama ?? '-' }}</div></td>
                                        <td>{{ $quotation->nama_projek ?? '-' }}</td>
                                        <td><div style="color:#6b7280; font-size:13px;">{{ optional($quotation->tanggal_penawaran)->format('d M Y') ?: '-' }}</div></td>
                                        <td><div style="font-weight:700;">Rp {{ number_format((float) $quotation->nilai_penawaran, 0, ',', '.') }}</div></td>
                                        <td>Rp {{ number_format((float) $quotation->hpp, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($quotation->status) }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <a href="{{ route('quotations.edit', $quotation->id) }}" class="action-btn" title="Edit">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>
                                            <a href="{{ route('quotations.history', $quotation->id) }}" class="action-btn" title="History" style="color: #0b7285;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            </a>
                                            <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus quotation ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn" title="Delete" style="color: #dc2626; cursor: pointer;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
