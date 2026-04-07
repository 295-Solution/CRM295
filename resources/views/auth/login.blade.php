<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login CRM295</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-start: #f6efe5;
            --bg-end: #ecf2e8;
            --ink: #232420;
            --muted: #5f665b;
            --line: #d8ddcf;
            --panel: #fffef9;
            --accent: #0b7285;
            --accent-strong: #0a5e6f;
            --danger: #c33131;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 82% 12%, #f7dabb 0, transparent 25%),
                radial-gradient(circle at 15% 25%, #d5ece0 0, transparent 28%),
                linear-gradient(140deg, var(--bg-start), var(--bg-end));
        }

        .card {
            width: min(440px, 100%);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 22px 42px rgba(35, 36, 32, 0.08);
        }

        h1 {
            margin: 0;
            font-family: 'Sora', sans-serif;
            font-size: clamp(24px, 4vw, 32px);
            letter-spacing: -0.03em;
        }

        .subtitle {
            margin: 10px 0 22px;
            color: var(--muted);
            line-height: 1.55;
        }

        .alert {
            margin-bottom: 14px;
            border: 1px solid color-mix(in srgb, var(--danger) 45%, white 55%);
            background: color-mix(in srgb, var(--danger) 10%, white 90%);
            color: #751b1b;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 700;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 13px;
            font-size: 15px;
            outline: none;
            background: #fff;
            transition: border-color 140ms ease, box-shadow 140ms ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(11, 114, 133, 0.14);
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 10px 0 16px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 15px;
            font-weight: 800;
            color: #f8fffd;
            background: linear-gradient(120deg, var(--accent), var(--accent-strong));
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(11, 114, 133, 0.24);
        }

        .btn:hover {
            filter: brightness(1.03);
        }

        .helper {
            margin-top: 14px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .helper code {
            background: #f4f6ef;
            border: 1px solid #e6eadf;
            border-radius: 6px;
            padding: 1px 5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Login CRM295</h1>
        <p class="subtitle">Masuk dulu untuk mengakses dashboard, leads, aktivitas, quotation, follow-up, dan laporan.</p>

        @if ($errors->any())
            <div class="alert">
                <strong>Login gagal.</strong>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>

            <div class="row">
                <label class="remember" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
            </div>

            <button class="btn" type="submit">Masuk ke CRM</button>
        </form>

    </main>
</body>
</html>
