<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Quotation {{ $quotation->nama_projek }}</title>
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
            max-width: 800px;
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
            padding: 12px 20px;
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
        
        .btn-warning {
            background: #fffbeb;
            color: #b45309;
            border-color: #fcd34d;
        }
        .btn-warning:hover {
            background: #fef3c7;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #4b5563;
        }

        .form-control, select, input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            background: #f9fafb;
            font-family: inherit;
            font-size: 14px;
            color: #1f2937;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-control:focus, select:focus, input:focus {
            outline: none;
            border-color: #1d6f78;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(29, 111, 120, 0.1);
        }

        .grid { display: grid; gap: 0 16px; grid-template-columns: 1fr 1fr; }
        @media (max-width: 720px) { .grid { grid-template-columns: 1fr; } }
        
        .action-bars {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            border-top: 1px solid #e9ecef;
            padding-top: 24px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')
        <main class="app-main">
    <div class="container">
        <div class="top">
            <h1 class="page-title">Update Quotation {{ $quotation->nama_projek }}</h1>
            <a class="btn btn-secondary" href="{{ route('clients.show', $quotation->client_id) }}">Kembali ke Client</a>
        </div>

        @if ($errors->any())
            <div class="panel" style="border-color:#f8b9ab; background:#fff1f2; color:#9f1239; margin-bottom:24px; padding:20px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="panel">
            <form method="POST" action="{{ route('quotations.update', $quotation) }}">
                @csrf
                @method('PUT')
                <div class="grid">
                    <div>
                        <label class="form-label" for="nama_projek">Jenis Projek</label>
                        <select class="form-control" id="nama_projek" name="nama_projek" required>
                            <option value="">-- Pilih Jenis Projek --</option>
                            <option value="CCTV" @selected(old('nama_projek', $quotation->nama_projek) == 'CCTV')>CCTV</option>
                            <option value="MCFA" @selected(old('nama_projek', $quotation->nama_projek) == 'MCFA')>MCFA</option>
                            <option value="Gate" @selected(old('nama_projek', $quotation->nama_projek) == 'Gate')>Gate</option>
                            <option value="Videotron" @selected(old('nama_projek', $quotation->nama_projek) == 'Videotron')>Videotron</option>
                            <option value="Smartboard" @selected(old('nama_projek', $quotation->nama_projek) == 'Smartboard')>Smartboard</option>
                            <option value="Smarthome" @selected(old('nama_projek', $quotation->nama_projek) == 'Smarthome')>Smarthome</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="tanggal_penawaran">Tanggal Penawaran</label>
                        <input class="form-control" id="tanggal_penawaran" type="date" name="tanggal_penawaran" value="{{ old('tanggal_penawaran', optional($quotation->tanggal_penawaran)->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="nilai_penawaran">Nilai Penawaran</label>
                        <input class="form-control" id="nilai_penawaran" type="number" min="0" step="0.01" name="nilai_penawaran" value="{{ old('nilai_penawaran', $quotation->nilai_penawaran) }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="hpp">HPP</label>
                        <input class="form-control" id="hpp" type="number" min="0" step="0.01" name="hpp" value="{{ old('hpp', $quotation->hpp) }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" @selected(old('status', $quotation->status)=='pending')>Pending</option>
                            <option value="nego" @selected(old('status', $quotation->status)=='nego')>Nego</option>
                            <option value="accepted" @selected(old('status', $quotation->status)=='accepted')>Accepted</option>
                            <option value="rejected" @selected(old('status', $quotation->status)=='rejected')>Rejected</option>
                        </select>
                    </div>
                </div>
                
                <div class="action-bars">
                    <button type="submit" class="btn btn-primary">Update Quotation</button>
                    <a href="{{ route('quotations.history', $quotation) }}" class="btn btn-warning">Lihat History</a>
                    <a href="{{ route('clients.show', $quotation->client_id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </section>
    </div>
        </main>
    </div>
</body>
</html>
