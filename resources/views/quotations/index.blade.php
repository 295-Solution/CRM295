<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotations - CRM295</title>
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

        .panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .summary .item {
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

        .summary .item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .summary .label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #697065;
            font-weight: 700;
            z-index: 1;
        }

        .summary .value {
            margin-top: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #1a1d1a;
            z-index: 1;
            letter-spacing: -0.03em;
        }

        .filters {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        input[type="text"], select {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 10px 14px;
            background: #f9fafb;
            font-family: inherit;
            font-size: 14px;
            color: #374151;
            transition: all 0.2s ease;
        }

        input[type="text"] {
            flex: 1;
            min-width: 250px;
        }

        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: #1d6f78;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(29, 111, 120, 0.1);
        }

        .table-wrap {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: auto;
            background: #fff;
            margin-top: 16px;
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

        .actions a, .actions button {
            color: #1d6f78;
            text-decoration: none;
            font-weight: 700;
            margin-right: 12px;
            font-size: 13px;
        }
        
        .actions a:hover {
            text-decoration: underline;
        }

        .pagination { margin-top: 16px; }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')
        <main class="app-main">
    <div class="container">
        <div class="top">
            <h1 class="page-title">Quotation Management</h1>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Quotation
            </a>
        </div>

        @if (session('success'))
            <div class="panel" style="border-color:#9ed8c1; background:#f0fdf4; color:#166534; padding:16px;">{{ session('success') }}</div>
        @endif

        <section class="summary" style="margin-bottom: 24px;">
            <div class="item">
                <div class="label">Total Quotation</div>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="item">
                <div class="label">Quotation Berjalan</div>
                <div class="value" style="color: #ea580c;">{{ number_format($summary['quotation_ongoing']) }}</div>
            </div>
            <div class="item">
                <div class="label">Deal Bulan Ini</div>
                <div class="value" style="color: #1e9d60;">{{ number_format($summary['deal_this_month']) }}</div>
            </div>
        </section>

        <section class="panel">
            <form method="GET" action="{{ route('quotations.index') }}" class="filters">
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Cari nomor quotation, nama client, perusahaan">
                <select name="status">
                    <option value="">Semua Status</option>
                    @foreach (['pending', 'nego', 'accepted', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">Cari</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Jenis Projek</th>
                            <th>Tanggal</th>
                            <th>Nilai Penawaran</th>
                            <th>HPP</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotations as $quotation)
                            <tr>
                                <td>
                                    @if($quotation->client)
                                        <a href="{{ route('clients.show', $quotation->client) }}" style="color:#1d6f78; font-weight:700; text-decoration:none;">{{ $quotation->client->nama }}</a>
                                    @else
                                        <span style="color:#aaa">-</span>
                                    @endif
                                </td>
                                <td>{{ $quotation->nama_projek ?: '-' }}</td>
                                <td>{{ optional($quotation->tanggal_penawaran)->format('d M Y') ?: '-' }}</td>
                                <td>Rp {{ number_format($quotation->nilai_penawaran, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($quotation->hpp ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span style="display:inline-block; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700; background:#f3f4f6; color:#374151;">{{ ucfirst($quotation->status) }}</span>
                                </td>
                                <td class="actions">
                                    <a href="{{ route('quotations.edit', $quotation) }}">Update</a>
                                    <a href="{{ route('quotations.history', $quotation) }}">History</a>
                                    <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:#dc2626; cursor:pointer; padding:0; font-family:inherit;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="color:#6b7280; text-align:center; padding:32px;">Belum ada quotation sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $quotations->links() }}</div>
        </section>
    </div>
        </main>
    </div>
</body>
</html>
