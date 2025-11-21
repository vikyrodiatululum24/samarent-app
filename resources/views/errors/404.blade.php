<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        :root { --bg:#f7f9fc; --card:#ffffff; --muted:#6b7280; --accent:#2563eb; }
        html,body { height:100%; margin:0; font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background:var(--bg); color:#111827; }
        .center { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
        .card { background:var(--card); padding:2.25rem; border-radius:12px; box-shadow:0 6px 25px rgba(15,23,42,0.08); max-width:760px; width:100%; text-align:center; }
        h1 { font-size:4.5rem; margin:0 0 .5rem; letter-spacing:-2px; color:#111827; }
        h2 { margin:0 0 .75rem; font-size:1.25rem; color:var(--muted); }
        p { margin:.5rem 0 1.5rem; color:var(--muted); }
        .actions { display:flex; gap:.75rem; justify-content:center; flex-wrap:wrap; }
        .btn { display:inline-block; padding:.6rem 1rem; border-radius:8px; text-decoration:none; color:#fff; background:var(--accent); font-weight:600; box-shadow:0 6px 16px rgba(37,99,235,0.18); }
        .btn.secondary { background:#f3f4f6; color:#111827; box-shadow:none; border:1px solid #e5e7eb; }
        footer { margin-top:1.25rem; color:#9ca3af; font-size:.875rem; }
        @media (max-width:420px){ h1{font-size:3rem} }
    </style>
</head>
<body>
    <main class="center">
        <div class="card" role="alert" aria-labelledby="err-code err-title">
            <h1 id="err-code">404</h1>
            <h2 id="err-title">Halaman Tidak Ditemukan</h2>
            <p>Maaf, halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dipindahkan atau dihapus.</p>

            <div class="actions">
                <a href="{{ url('/') }}" class="btn">Kembali ke Beranda</a>
                <a href="{{ url()->previous() }}" class="btn secondary">Kembali</a>
            </div>

            <footer>
                @if(config('app.debug'))
                    <div style="margin-top:.75rem;color:#6b7280;font-size:.85rem;">
                        Debug mode aktif — periksa log untuk detail.
                    </div>
                @endif
            </footer>
        </div>
    </main>
</body>
</html>
