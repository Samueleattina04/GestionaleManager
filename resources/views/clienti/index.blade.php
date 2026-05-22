@extends('layouts.app')
@section('titolo', 'Clienti')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Clienti</h5>
    @can('crea_clienti')
    <a href="{{ route('clienti.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i>Nuovo Cliente
    </a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-end">
            <div class="col-sm-5">
                <input type="text" name="cerca" class="form-control form-control-sm" placeholder="Cerca per nome, email, telefono..." value="{{ request('cerca') }}">
            </div>
            <div class="col-sm-2">
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Tutti i tipi</option>
                    <option value="azienda" {{ request('tipo') === 'azienda' ? 'selected' : '' }}>Azienda</option>
                    <option value="privato" {{ request('tipo') === 'privato' ? 'selected' : '' }}>Privato</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Cerca
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Codice</th><th>Ragione Sociale</th><th>Città</th><th>Telefono</th><th>Email</th><th>Tipo</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($clienti as $cliente)
                <tr>
                    <td><small class="text-muted">{{ $cliente->codice }}</small></td>
                    <td>
                        <a href="{{ route('clienti.show', $cliente) }}" class="text-decoration-none fw-medium">
                            {{ $cliente->ragione_sociale }}
                        </a>
                        @if($cliente->referente)
                            <div class="text-muted small">{{ $cliente->referente }}</div>
                        @endif
                    </td>
                    <td>{{ $cliente->citta }} {{ $cliente->provincia ? '('.$cliente->provincia.')' : '' }}</td>
                    <td>{{ $cliente->telefono ?? $cliente->cellulare ?? '—' }}</td>
                    <td>{{ $cliente->email ?? '—' }}</td>
                    <td>
                        <span class="badge bg-light text-dark">
                            {{ $cliente->tipo === 'azienda' ? 'Azienda' : 'Privato' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('clienti.show', $cliente) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @can('modifica_clienti')
                            <a href="{{ route('clienti.edit', $cliente) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-3 d-block mb-2"></i>Nessun cliente trovato
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $clienti->links() }}</div>
</div>
@endsection
