<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients - CRM295</title>
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

        .panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
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
                <div>
                    <h1 class="page-title">Client Workspace</h1>
                    <p class="page-subtitle">Semua client yang dimiliki tim Anda dalam satu daftar yang cepat dicari.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('clients.create') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Client
                </a>
            </div>

            @if (session('success'))
                <div class="panel" style="border-color:#9ed8c1; background:#f0fdf4; color:#166534; padding:16px; margin-bottom:24px;">{{ session('success') }}</div>
            @endif

            <section class="panel">
                <form method="GET" action="{{ route('clients.index') }}" class="filters">
                    <input id="q" type="text" name="q" value="{{ $search }}" placeholder="Ketik nama, perusahaan, atau nomor WA...">
                    <button class="btn btn-primary" type="submit">Cari Client</button>
                    @if($search)
                        <a class="btn" href="{{ route('clients.index') }}">Reset</a>
                    @endif
                </form>
            </section>

            <section class="panel" style="padding:0; border:none; background:transparent; box-shadow:none;">
                @if ($clients->isEmpty())
                    <div class="empty">Belum ada data client. Tambahkan client pertama Anda sekarang.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Nomor WA</th>
                                    <th>Sumber Client</th>
                                    <th>Jenis Bisnis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td><a href="{{ route('clients.show', $client) }}" style="color:#1d6f78; font-weight:700; text-decoration:none;">{{ $client->nama }}</a></td>
                                        <td>{{ $client->perusahaan ?: '-' }}</td>
                                        <td>{{ $client->nomor_wa }}</td>
                                        <td><span class="chip">{{ ucfirst($client->sumber_client) }}</span></td>
                                        <td>{{ $client->jenis_bisnis ?: '-' }}</td>
                                        <td class="actions">
                                            <a href="{{ route('clients.show', $client) }}">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">{{ $clients->links() }}</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
