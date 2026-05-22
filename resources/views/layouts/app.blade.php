<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titolo', 'Dashboard') - {{ $tenant->ragione_sociale ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --colore-primario: {{ $tenant->colore_primario ?? '#2563eb' }};
            --colore-secondario: {{ $tenant->colore_secondario ?? '#1e40af' }};
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--colore-primario) 0%, var(--colore-secondario) 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand { padding: 1.5rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand img { max-height: 45px; }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1.1rem; }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-section { padding: 0.5rem 1.25rem 0.25rem; color: rgba(255,255,255,0.5); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.6rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            border-radius: 0;
            transition: all 0.2s;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.15);
        }
        .sidebar-nav .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 0 1.5rem; height: 60px; display: flex; align-items: center; justify-content: between; position: sticky; top: 0; z-index: 999; }
        .page-content { padding: 1.5rem; }
        .card { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .card-header { background: transparent; border-bottom: 1px solid #e5e7eb; font-weight: 600; }
        .btn-primary { background-color: var(--colore-primario); border-color: var(--colore-primario); }
        .btn-primary:hover { background-color: var(--colore-secondario); border-color: var(--colore-secondario); }
        .badge-stato-da_assegnare { background: #fef3c7; color: #92400e; }
        .badge-stato-assegnato { background: #dbeafe; color: #1e40af; }
        .badge-stato-in_corso { background: #d1fae5; color: #065f46; }
        .badge-stato-completato { background: #d1fae5; color: #065f46; }
        .badge-stato-annullato { background: #f3f4f6; color: #6b7280; }
        .badge-priorita-urgente { background: #fee2e2; color: #991b1b; }
        .badge-priorita-normale { background: #dbeafe; color: #1e40af; }
        .badge-priorita-bassa { background: #f3f4f6; color: #6b7280; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        .notification-badge { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; }
        .widget-card { border-radius: 12px; border: none; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
        .form-control:focus, .form-select:focus { border-color: var(--colore-primario); box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.1); }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @if($tenant->logo_url ?? false)
                <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->ragione_sociale }}" class="mb-2">
            @endif
            <h5>{{ $tenant->ragione_sociale ?? config('app.name') }}</h5>
        </div>
        <div class="sidebar-nav">
            @php $user = auth()->user(); @endphp

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            @can('visualizza_clienti')
            <div class="sidebar-section mt-2">Anagrafica</div>
            <a href="{{ route('clienti.index') }}" class="nav-link {{ request()->routeIs('clienti.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Clienti
            </a>
            <a href="{{ route('fornitori.index') }}" class="nav-link {{ request()->routeIs('fornitori.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Fornitori
            </a>
            @endcan

            @can('visualizza_interventi')
            <div class="sidebar-section mt-2">Operativo</div>
            <a href="{{ route('interventi.index') }}" class="nav-link {{ request()->routeIs('interventi.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Interventi
            </a>
            @endcan

            @can('visualizza_preventivi')
            <a href="{{ route('preventivi.index') }}" class="nav-link {{ request()->routeIs('preventivi.*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Preventivi
            </a>
            @endcan

            @can('visualizza_contratti')
            <a href="{{ route('contratti.index') }}" class="nav-link {{ request()->routeIs('contratti.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check"></i> Contratti
            </a>
            @endcan

            @can('visualizza_magazzino')
            <div class="sidebar-section mt-2">Magazzino</div>
            <a href="{{ route('articoli.index') }}" class="nav-link {{ request()->routeIs('articoli.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Articoli
            </a>
            <a href="{{ route('magazzini.index') }}" class="nav-link {{ request()->routeIs('magazzini.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Depositi
            </a>
            @endcan

            @can('visualizza_fatture')
            <div class="sidebar-section mt-2">Amministrazione</div>
            <a href="{{ route('fatture.index') }}" class="nav-link {{ request()->routeIs('fatture.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Fatture
            </a>
            <a href="{{ route('fatture.scadenzario') }}" class="nav-link">
                <i class="bi bi-calendar-check"></i> Scadenzario
            </a>
            <a href="{{ route('fatture.registro-iva') }}" class="nav-link">
                <i class="bi bi-journal-text"></i> Registro IVA
            </a>
            @endcan

            @can('visualizza_report')
            <div class="sidebar-section mt-2">Analisi</div>
            <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Report
            </a>
            <a href="{{ route('gps.mappa') }}" class="nav-link {{ request()->routeIs('gps.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Mappa Tecnici
            </a>
            @endcan

            @role('titolare')
            <div class="sidebar-section mt-2">Configurazione</div>
            <a href="{{ route('impostazioni') }}" class="nav-link {{ request()->routeIs('impostazioni*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Impostazioni
            </a>
            @endrole
        </div>
    </nav>

    <!-- Contenuto principale -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="btn btn-link text-dark d-md-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="d-flex align-items-center flex-grow-1">
                <h6 class="mb-0 fw-semibold text-muted">@yield('titolo', 'Dashboard')</h6>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notifiche -->
                <div class="position-relative">
                    <a href="{{ route('notifiche.index') }}" class="btn btn-link text-dark p-1">
                        <i class="bi bi-bell fs-5"></i>
                        @php $nonLette = auth()->user()->notificheNonLette(); @endphp
                        @if($nonLette > 0)
                            <span class="notification-badge">{{ $nonLette > 9 ? '9+' : $nonLette }}</span>
                        @endif
                    </a>
                </div>
                <!-- Utente -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark p-1 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                        <span class="d-none d-md-inline fw-medium" style="font-size:0.875rem">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down" style="font-size:0.75rem"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('profilo') }}"><i class="bi bi-person me-2"></i>Profilo</a></li>
                        @role('titolare')
                        <li><a class="dropdown-item" href="{{ route('impostazioni') }}"><i class="bi bi-gear me-2"></i>Impostazioni</a></li>
                        @endrole
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Esci
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="page-content pb-0">
            @if(session('successo'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('successo') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('errore'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('errore') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Contenuto pagina -->
        <main class="page-content">
            @yield('contenuto')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
