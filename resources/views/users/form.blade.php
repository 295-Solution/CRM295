<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} - CRM295</title>
    @include('partials.sidebar-styles')
    <style>
        body { font-family: Manrope, sans-serif; margin: 0; background: #f3f1eb; color: #1f201d; }
        .container { max-width: 760px; margin: 0 auto; padding: 24px 16px 40px; }
        .panel { background: #fffdf8; border: 1px solid #d9ddce; border-radius: 14px; padding: 14px; }
        .top { display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
        h1 { margin:0; }
        .btn { border: 1px solid #d9ddce; border-radius: 10px; padding: 9px 12px; text-decoration: none; color: #1f201d; background: #fff; font-weight: 700; }
        .btn-primary { background: #1d6f78; color: #f5fffd; border-color: #175962; }
        label { display:block; margin-bottom:6px; font-size:12px; text-transform:uppercase; color:#697065; }
        input, select { width:100%; border:1px solid #d9ddce; border-radius:10px; padding:10px; margin-bottom:10px; }
        .grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .full { grid-column: 1 / -1; }
        .error-box { border:1px solid #f4c0b5; background:#ffe9e5; color:#7c2516; border-radius:10px; padding:10px; margin-bottom:10px; }
        .footer { display:flex; justify-content:space-between; gap:8px; margin-top:10px; flex-wrap:wrap; }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')
        <main class="app-main">
    <div class="container">
        <div class="top">
            <h1>{{ $pageTitle }}</h1>
            <a class="btn" href="{{ route('users.index') }}">Kembali</a>
        </div>

        <section class="panel">
            @if ($errors->any())
                <div class="error-box">
                    <strong>Periksa input berikut:</strong>
                    <ul style="margin:8px 0 0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}">
                @csrf
                @if ($formMethod !== 'POST')
                    @method($formMethod)
                @endif

                <div class="grid">
                    <div>
                        <label for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $userModel->name) }}" required>
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $userModel->email) }}" required>
                    </div>

                    <div>
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            @foreach (['superadmin', 'admin', 'sales'] as $role)
                                <option value="{{ $role }}" @selected(old('role', $userModel->role ?: 'sales') === $role)>{{ strtoupper($role) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="password">Password {{ $formMethod === 'POST' ? '' : '(Kosongkan jika tidak diubah)' }}</label>
                        <input id="password" type="password" name="password" {{ $formMethod === 'POST' ? 'required' : '' }}>
                    </div>

                    <div class="full">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" {{ $formMethod === 'POST' ? 'required' : '' }}>
                    </div>
                </div>

                <div class="footer">
                    <a class="btn" href="{{ route('users.index') }}">Batal</a>
                    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
                </div>
            </form>
        </section>
    </div>
        </main>
    </div>
</body>
</html>
