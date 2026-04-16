<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Client {{ $client->nama }} - CRM295</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 32px 24px 60px;
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
            margin: 8px 0 24px;
            color: #697065;
            font-size: 15px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }

        .form-grid {
            display: grid;
            gap: 0 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        
        .form-grid .field-full {
            grid-column: span 2;
        }
        
        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .field-full { grid-column: span 1; }
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
        
        .error {
            margin-top: -18px;
            margin-bottom: 24px;
            color: #dc2626;
            font-size: 12px;
            font-weight: 500;
        }

        .action-bars {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            border-top: 1px solid #e9ecef;
            padding-top: 24px;
        }
        @media (max-width: 600px) { .action-bars { flex-direction: column; } .btn { width: 100%; } }
    </style>
</head>

<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <div class="container">
            <h1 class="page-title">Edit Client: {{ $client->nama }}</h1>
            <p class="page-subtitle">Perbarui data client untuk menjaga kontak yang rapi dan konsisten.</p>

            <section class="panel">
                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf
                    @method('PUT')
                    
                    @php
                       $isCustomBusiness = !in_array($client->jenis_bisnis, $businessTypeOptions) && !empty($client->jenis_bisnis);
                       $selectedBusinessType = old('jenis_bisnis', $isCustomBusiness ? $customBusinessTypeValue : $client->jenis_bisnis);
                       $customBusinessValue = old('jenis_bisnis_custom', $isCustomBusiness ? $client->jenis_bisnis : '');
                    @endphp

                    <div class="form-grid">
                        <div class="field-full">
                            <label class="form-label" for="nama">Nama</label>
                            <input class="form-control" id="nama" type="text" name="nama" value="{{ old('nama', $client->nama) }}" required>
                            @error('nama') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="perusahaan">Perusahaan</label>
                            <input class="form-control" id="perusahaan" type="text" name="perusahaan" value="{{ old('perusahaan', $client->perusahaan) }}">
                            @error('perusahaan') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="nomor_wa">Nomor WA</label>
                            <input class="form-control" id="nomor_wa" type="text" name="nomor_wa" value="{{ old('nomor_wa', $client->nomor_wa) }}" required>
                            @error('nomor_wa') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field-full">
                            <label class="form-label" for="sumber_client">Sumber Client</label>
                            <select class="form-control" id="sumber_client" name="sumber_client" required>
                                <option value="">Pilih sumber client</option>
                                @foreach ($sumberClientOptions as $sumberClient)
                                    <option value="{{ $sumberClient }}" @selected(old('sumber_client', $client->sumber_client) === $sumberClient)>{{ ucfirst($sumberClient) }}</option>
                                @endforeach
                            </select>
                            @error('sumber_client') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field-full">
                            <label class="form-label" for="jenis_bisnis">Jenis Bisnis</label>
                            <select class="form-control" id="jenis_bisnis" name="jenis_bisnis" required>
                                <option value="">Pilih jenis bisnis</option>
                                @foreach ($businessTypeOptions as $businessType)
                                    <option value="{{ $businessType }}" @selected($selectedBusinessType === $businessType)>{{ ucfirst($businessType) }}</option>
                                @endforeach
                                <option value="{{ $customBusinessTypeValue }}" @selected($selectedBusinessType === $customBusinessTypeValue)>Ketik sendiri</option>
                            </select>
                            @error('jenis_bisnis') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field-full" id="jenis_bisnis_custom_wrapper" style="display:none;">
                            <label class="form-label" for="jenis_bisnis_custom">Ketik Jenis Bisnis</label>
                            <input class="form-control" id="jenis_bisnis_custom" type="text" name="jenis_bisnis_custom" value="{{ $customBusinessValue }}" placeholder="Contoh: kesehatan, retail, manufaktur">
                            @error('jenis_bisnis_custom') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="action-bars">
                        <button class="btn btn-primary" type="submit">Update Client</button>
                        <a class="btn btn-secondary" href="{{ route('clients.show', $client) }}">Batal</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
<script>
    (() => {
        const jenisBisnisSelect = document.getElementById('jenis_bisnis');
        const customWrapper = document.getElementById('jenis_bisnis_custom_wrapper');
        const customInput = document.getElementById('jenis_bisnis_custom');
        const customOptionValue = @json($customBusinessTypeValue);

        if (!jenisBisnisSelect || !customWrapper || !customInput) {
            return;
        }

        const syncCustomField = () => {
            const isCustom = jenisBisnisSelect.value === customOptionValue;
            customWrapper.style.display = isCustom ? 'block' : 'none';
            customInput.required = isCustom;

            if (!isCustom) {
                customInput.value = '';
            }
        };

        jenisBisnisSelect.addEventListener('change', syncCustomField);
        syncCustomField();
    })();
</script>
</body>
</html>
