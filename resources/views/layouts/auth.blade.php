<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titolo', 'Accesso') - GestionaleManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%); min-height: 100vh; display: flex; align-items: center; }
        .auth-card { max-width: 460px; width: 100%; margin: auto; }
        .card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .card-body { padding: 2.5rem; }
        .auth-logo { text-align: center; margin-bottom: 2rem; }
        .auth-logo i { font-size: 3rem; color: #2563eb; }
        .auth-logo h4 { font-weight: 700; color: #1e293b; margin-top: 0.5rem; }
        .btn-primary { background: #2563eb; border-color: #2563eb; padding: 0.65rem; font-weight: 500; }
        .btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
        .form-control { border-radius: 8px; border: 1px solid #d1d5db; padding: 0.65rem 0.875rem; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-label { font-weight: 500; color: #374151; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="auth-card mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="auth-logo">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <h4>GestionaleManager</h4>
                        <p class="text-muted mb-0" style="font-size:0.875rem">@yield('sottotitolo')</p>
                    </div>
                    @if(session('stato'))
                        <div class="alert alert-success">{{ session('stato') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    @yield('contenuto')
                </div>
            </div>
            <p class="text-center text-white mt-3" style="font-size:0.8rem">
                &copy; {{ date('Y') }} GestionaleManager. Tutti i diritti riservati.
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
