@extends('layouts.superadmin')
@section('titolo', 'Dashboard Super Admin')

@section('contenuto')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <h2 class="fw-bold text-primary">{{ $tenantAttivi }}</h2>
            <p class="text-muted mb-0">Aziende Attive</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <h2 class="fw-bold">{{ $tenantTotali }}</h2>
            <p class="text-muted mb-0">Totale Aziende</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <h2 class="fw-bold text-success">{{ $utentiTotali }}</h2>
            <p class="text-muted mb-0">Utenti Totali</p>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Ultime Aziende Registrate</span>
        <a href="{{ route('superadmin.tenant.index') }}" class="btn btn-sm btn-primary">Gestisci tutte</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Azienda</th><th>Email</th><th>Piano</th><th>Stato</th><th>Registrata</th></tr></thead>
            <tbody>
                @foreach($ultimiTenant as $t)
                <tr>
                    <td><strong>{{ $t->ragione_sociale }}</strong></td>
                    <td>{{ $t->email }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($t->piano) }}</span></td>
                    <td>
                        @if($t->attivo)
                            <span class="badge bg-success">Attiva</span>
                        @else
                            <span class="badge bg-danger">Disattivata</span>
                        @endif
                    </td>
                    <td>{{ $t->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
