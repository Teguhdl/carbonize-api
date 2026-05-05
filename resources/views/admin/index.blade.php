<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbonize — Admin Panel</title>
    <meta name="description" content="Carbonize Admin Panel — kelola data emisi karbon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div id="toast-container"></div>

{{-- ══════════════════════ LOGIN VIEW ══════════════════════════ --}}
<div id="login-view">
    <div class="glass login-box">
        <h1><i class="fa-solid fa-leaf"></i> Carbonize Admin</h1>
        <p style="color:var(--muted);margin-bottom:2rem;font-size:0.9rem">Masuk untuk mengakses panel kontrol API</p>
        <form id="login-form">
            <label>Email</label>
            <input type="email" id="login-email" class="form-control" placeholder="admin@example.com" required>
            <label>Password</label>
            <input type="password" id="login-password" class="form-control" placeholder="••••••••" required>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem">
                Sign In
            </button>
        </form>
    </div>
</div>

{{-- ══════════════════════ APP VIEW ════════════════════════════ --}}
<div id="app-view" class="hidden">

    @include('admin.partials.sidebar')

    <main class="main-content">
        @include('admin.partials.views')
    </main>

</div>

{{-- ══════════════════════ MODALS ══════════════════════════════ --}}
@include('admin.partials.modals')

<script src="{{ asset('js/admin/api.js') }}"></script>
<script src="{{ asset('js/admin/app.js') }}"></script>

</body>
</html>
