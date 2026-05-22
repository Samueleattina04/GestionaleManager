@extends('layouts.superadmin')
@section('titolo', 'Gestione Aziende')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Aziende Registrate</h5>
    <a href="{{ route('superadmin.tenant.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuova Azienda
    </a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Azienda</th><th>Email</th><th>Piano</th>
                    <th>Utenti</th><th>Stato</th><th>Scadenza</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $t)
                <tr>
                    <td>
                        <strong>{{ $t->ragione_sociale }}</strong>
                        <div class="text-muted small">{{ $t->slug }}</div>
                    </td>
                    <td>{{ $t->email }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($t->piano) }}</span></td>
                    <td>{{ $t->utenti_count }}</td>
                    <td>
                        @if($t->attivo)
                            <span class="badge bg-success">Attiva</span>
                        @else
                            <span class="badge bg-danger">Disattivata</span>
                        @endif
                    </td>
                    <td>{{ $t->scadenza_abbonamento?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('superadmin.tenant.edit', $t) }}" class="btn btn-xs btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($t->attivo)
                            <form action="{{ route('superadmin.tenant.disattiva', $t) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-danger btn-sm">
                                    <i class="bi bi-pause-circle"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('superadmin.tenant.attiva', $t) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-success btn-sm">
                                    <i class="bi bi-play-circle"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Nessuna azienda registrata</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $tenants->links() }}</div>
</div>
@endsection
