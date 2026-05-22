@extends('layouts.app')
@section('titolo', 'Movimenti - ' . $articolo->nome)
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Movimenti: {{ $articolo->nome }}</h1>
    <a href="{{ route('articoli.show', $articolo) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="da" value="{{ request('da') }}" class="form-control" placeholder="Da data">
            </div>
            <div class="col-md-3">
                <input type="date" name="a" value="{{ request('a') }}" class="form-control" placeholder="A data">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-select">
                    <option value="">Tutti i tipi</option>
                    <option value="carico" {{ request('tipo') === 'carico' ? 'selected' : '' }}>Carico</option>
                    <option value="scarico" {{ request('tipo') === 'scarico' ? 'selected' : '' }}>Scarico</option>
                    <option value="trasferimento" {{ request('tipo') === 'trasferimento' ? 'selected' : '' }}>Trasferimento</option>
                    <option value="rettifica" {{ request('tipo') === 'rettifica' ? 'selected' : '' }}>Rettifica</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-secondary w-100">Filtra</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th class="text-center">Quantità</th>
                    <th>Magazzino origine</th>
                    <th>Magazzino destinazione</th>
                    <th>Riferimento</th>
                    <th>Note</th>
                    <th>Registrato da</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimenti as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $mov->tipo === 'carico' ? 'success' : ($mov->tipo === 'scarico' ? 'danger' : ($mov->tipo === 'trasferimento' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($mov->tipo) }}
                        </span>
                    </td>
                    <td class="text-center fw-bold">{{ number_format($mov->quantita, 2) }} {{ $articolo->unita_misura }}</td>
                    <td>{{ $mov->magazzinoOrigine?->nome ?? '-' }}</td>
                    <td>{{ $mov->magazzinoDestinazione?->nome ?? '-' }}</td>
                    <td>{{ $mov->riferimento ?? '-' }}</td>
                    <td>{{ $mov->note ?? '-' }}</td>
                    <td>{{ $mov->registratoDa?->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Nessun movimento registrato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movimenti->hasPages())
    <div class="card-footer">{{ $movimenti->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
