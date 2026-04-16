<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Quotation Baru - CRM295</title>
    @include('partials.sidebar-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .page-title {
            margin: 0 0 24px;
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

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #4b5563;
        }

        .form-control, select, textarea {
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

        .form-control:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #1d6f78;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(29, 111, 120, 0.1);
        }

        .select2-container .select2-selection--single {
            height: 48px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            display: flex;
            align-items: center;
            background: #f9fafb;
            padding: 0 8px;
        }
        
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1d6f78;
            background: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937;
            font-size: 14px;
        }

        .action-bars {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            border-top: 1px solid #e9ecef;
            padding-top: 24px;
        }
        
        @media (max-width: 600px) {
            .action-bars {
                flex-direction: column;
            }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <div class="container">
            <h1 style="margin-top:0;">Buat Quotation Baru</h1>
            <section class="panel">
                <form method="POST" action="{{ route('quotations.store') }}">
                    @csrf
                    
                    <div style="margin-bottom: 15px;">
                        <label for="client_id" class="form-label">Client</label>
                        @if($selectedClientId)
                            <input type="hidden" name="client_id" value="{{ $selectedClientId }}">
                            <select class="form-control select2-client" disabled>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" @selected($c->id == $selectedClientId)>{{ $c->nama }} @if($c->perusahaan) - {{ $c->perusahaan }} @endif</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control select2-client" name="client_id" required>
                                <option value="">-- Pilih Client --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>{{ $c->nama }} @if($c->perusahaan) - {{ $c->perusahaan }} @endif</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="tanggal_penawaran" class="form-label">Tanggal Penawaran</label>
                        <input type="date" class="form-control" id="tanggal_penawaran" name="tanggal_penawaran" value="{{ old('tanggal_penawaran', date('Y-m-d')) }}" required>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="nama_projek" class="form-label">Nama Projek</label>
                        <input type="text" class="form-control" id="nama_projek" name="nama_projek" value="{{ old('nama_projek') }}" required>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="nilai_penawaran" class="form-label">Nilai Penawaran</label>
                        <input type="number" class="form-control" id="nilai_penawaran" name="nilai_penawaran" value="{{ old('nilai_penawaran') }}" required>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="hpp" class="form-label">HPP</label>
                        <input type="number" class="form-control" id="hpp" name="hpp" value="{{ old('hpp') }}" required>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required style="padding: 10px;">
                            <option value="pending" @selected(old('status')=='pending')>Pending</option>
                            <option value="nego" @selected(old('status')=='nego')>Nego</option>
                            <option value="accepted" @selected(old('status')=='accepted')>Accepted</option>
                            <option value="rejected" @selected(old('status')=='rejected')>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="action-bars">
                        <button type="submit" class="btn btn-primary">Simpan Quotation</button>
                        <a href="{{ $selectedClientId ? route('clients.show', $selectedClientId) : route('quotations.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-client').select2({
            placeholder: "-- Pilih Client --",
            allowClear: true
        });
    });
</script>
</body>
</html>
