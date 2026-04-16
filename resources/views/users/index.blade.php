<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen User - CRM295</title>
    @include('partials.sidebar-styles')
    <style>
        body { font-family: Manrope, sans-serif; margin: 0; background: #f3f1eb; color: #1f201d; }
        .container { max-width: 1100px; margin: 0 auto; padding: 24px 16px 40px; }
        .top { display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
        h1 { margin: 0; font-size: 30px; }
        .btn { border: 1px solid #d9ddce; border-radius: 10px; padding: 8px 12px; text-decoration: none; color: #1f201d; background: #fff; font-weight: 700; }
        .btn-primary { background: #1d6f78; color: #f5fffd; border-color: #175962; }
        .btn-danger { background: #fff1ee; border-color: #f1c7bf; color: #982f1e; }
        .panel { background: #fffdf8; border: 1px solid #d9ddce; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        input { border: 1px solid #d9ddce; border-radius: 10px; padding: 9px 10px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border-bottom: 1px solid #d9ddce; padding: 10px; text-align: left; font-size: 14px; }
        th { font-size: 12px; text-transform: uppercase; color: #697065; }
        .badge { display:inline-block; border-radius:999px; padding:4px 9px; font-size:12px; font-weight:700; }
        .pagination { margin-top: 10px; }
        .msg { margin-bottom: 10px; padding: 10px; border-radius: 10px; }
        .ok { background:#eaf8ef; border:1px solid #bfe5cb; color:#195230; }
        .err { background:#ffe9e5; border:1px solid #f4c0b5; color:#7c2516; }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')
        <main class="app-main">
    <div class="container">
        <div class="top">
            <h1>Manajemen User</h1>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a class="btn btn-primary" href="{{ route('users.create') }}">Tambah User</a>
            </div>
        </div>

        @if (session('success'))
            <div class="msg ok">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="msg err">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form method="GET" action="{{ route('users.index') }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:10px;">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama/email/role" style="min-width:280px;">
                <button class="btn" type="submit">Cari</button>
                @if ($search !== '')
                    <a class="btn" href="{{ route('users.index') }}">Reset</a>
                @endif
            </form>

            <div style="overflow:auto; border:1px solid #d9ddce; border-radius:10px;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $item)
                            @php
                                $roleColor = match ($item->role) {
                                    'superadmin' => '#6a1b9a',
                                    'admin' => '#1d6f78',
                                    default => '#697065',
                                };
                            @endphp
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td><span class="badge" style="background:{{ $roleColor }}22; color:{{ $roleColor }};">{{ strtoupper($item->role) }}</span></td>
                                <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                        <a class="btn" href="{{ route('users.edit', $item) }}">Edit</a>
                                        <form method="POST" action="{{ route('users.destroy', $item) }}" onsubmit="return confirm('Hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="color:#697065;">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $users->links() }}</div>
        </section>
    </div>
        </main>
    </div>
</body>
</html>
