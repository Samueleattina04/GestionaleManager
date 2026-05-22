@extends('layouts.app')
@section('titolo', $magazzino->nome)
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $magazzino->nome }}</h1>
    <div>
        <a href="{{ route('magazzini.inventario', $magazzino) }}" class="btn btn-outline-success me-2">
            <i class="bi bi-clipboard-check me-1"></i>Inventario
        </a>
        <a href="{{ route('magazzini.edit', $magazzino) }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <a href="{{ route('magazzini.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Informazioni</strong>
                @if($magazzino->principale)
                    <span class="badge bg-primary">Principale</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Indirizzo</dt>
                    <dd class="col-7">{{ $magazzino->indirizzo ?? '-' }}</dd>
                    <dt class="col-5">Città</dt>
                    <dd class="col-7">{{ $magazzino->citta ?? '-' }}</dd>
                    <dt class="col-5">Stato</dt>
                    <dd class="col-7">
                        @if($magazzino->attivo ?? true)
                            <span class="badge bg-success">Attivo</span>
                        @else
                            <span class="badge bg-secondary">Inattivo</span>
                        @endif
                    </dd>
                </dl>
                @if($magazzino->descrizione)
                    <hr>
                    <p class="mb-0 text-muted small">{{ $magazzino->descrizione }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-primary">{{ $giacenze->total() }}</h2>
                        <p class="text-muted mb-0">Articoli presenti</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-danger">{{ $giacenze->where('quantita', '<', 'scorta_minima')->count() }}</h2>
                        <p class="text-muted mb-0">Sotto scorta</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Giacenze articoli</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Codice</th>
                    <th>Articolo</th>
                    <th>Categoria</th>
                    <th class="text-center">Quantità</th>
                    <th>U.M.</th>
                    <th>Scorta min.</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($giacenze as $giacenza)
                @php $sottoScorta = $giacenza->quantita < ($giacenza->articolo->scorta_minima ?? 0); @endphp
                <tr class="{{ $sottoScorta ? 'table-warning' : '' }}">
                    <td><code>{{ $giacenza->articolo->codice }}</code></td>
                    <td>{{ $giacenza->articolo->nome }}</td>
                    <td>{{ $giacenza->articolo->categoria->nome ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ number_format($giacenza->quantita, 2) }}</td>
                    <td>{{ $giacenza->articolo->unita_misura }}</td>
                    <td>{{ $giacenza->articolo->scorta_minima ?? 0 }}</td>
                    <td>
                        @if($sottoScorta)
                            <span class="badge bg-danger">Sotto scorta</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('articoli.show', $giacenza->articolo) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Nessun articolo in questo magazzino.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($giacenze->hasPages())
    <div class="card-footer">{{ $giacenze->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
