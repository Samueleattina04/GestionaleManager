<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titolo', 'Super Admin') - GestionaleManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; }
        .sidebar { width: 250px; min-height: 100vh; background: #0f172a; position: fixed; top: 0; left: 0; z-index: 1000; }
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; }
        .sidebar-brand small { color: #94a3b8; font-size: 0.75rem; }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-nav .nav-link { color: #94a3b8; padding: 0.65rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color: #fff; background: rgba(255,255,255,0.08); }
        .main-content { margin-left: 250px; min-height: 100vh; }
        .topbar { background: #fff; padding: 0 1.5rem; height: 60px; display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .page-content { padding: 1.5rem; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-shield-lock me-2"></i>Super Admin</h5>
            <small>GestionaleManager</small>
        </div>
        <div class="sidebar-nav">
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('superadmin.tenant.index') }}" class="nav-link {{ request()->routeIs('superadmin.tenant.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Aziende (Tenant)
            </a>
            <a href="{{ route('superadmin.statistiche') }}" class="nav-link">
                <i class="bi bi-bar-chart"></i> Statistiche
            </a>
            <a href="{{ route('superadmin.log') }}" class="nav-link">
                <i class="bi bi-journal-text"></i> Log Attività
            </a>
        </div>
    </nav>
    <div class="main-content">
        <header class="topbar">
            <span class="fw-semibold text-muted">@yield('titolo')</span>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Esci
                    </button>
                </form>
            </div>
        </header>
        <main class="page-content">
            @if(session('successo'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('successo') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('contenuto')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
