@extends('layouts.app')
@section('titolo', $articolo->nome)
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $articolo->nome }}</h1>
    <div>
        <a href="{{ route('articoli.edit', $articolo) }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <a href="{{ route('articoli.movimenti', $articolo) }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left-right me-1"></i>Movimenti
        </a>
        <a href="{{ route('articoli.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Informazioni</strong>
                @if($articolo->attivo)
                    <span class="badge bg-success">Attivo</span>
                @else
                    <span class="badge bg-secondary">Inattivo</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-6">Codice</dt>
                    <dd class="col-6"><code>{{ $articolo->codice }}</code></dd>
                    <dt class="col-6">Categoria</dt>
                    <dd class="col-6">{{ $articolo->categoria->nome ?? '-' }}</dd>
                    <dt class="col-6">Unità misura</dt>
                    <dd class="col-6">{{ strtoupper($articolo->unita_misura) }}</dd>
                    <dt class="col-6">IVA</dt>
                    <dd class="col-6">{{ $articolo->aliquota_iva }}%</dd>
                    <dt class="col-6">Scorta min.</dt>
                    <dd class="col-6">{{ $articolo->scorta_minima ?? 0 }}</dd>
                </dl>
                @if($articolo->descrizione)
                <hr>
                <p class="text-muted small mb-0">{{ $articolo->descrizione }}</p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Prezzi</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-6">Prezzo acquisto</dt>
                    <dd class="col-6">€ {{ number_format($articolo->prezzo_acquisto ?? 0, 2, ',', '.') }}</dd>
                    <dt class="col-6">Prezzo vendita</dt>
                    <dd class="col-6 fw-bold text-success">€ {{ number_format($articolo->prezzo_vendita ?? 0, 2, ',', '.') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Giacenze per magazzino</strong></div>
            <ul class="list-group list-group-flush">
                @forelse($articolo->giacenze as $giacenza)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $giacenza->magazzino->nome }}</span>
                    <span class="fw-bold {{ $giacenza->quantita < ($articolo->scorta_minima ?? 0) ? 'text-danger' : 'text-success' }}">
                        {{ number_format($giacenza->quantita, 2) }} {{ $articolo->unita_misura }}
                    </span>
                </li>
                @empty
                <li class="list-group-item text-muted text-center">Nessuna giacenza registrata.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Ultimi movimenti</strong>
                <a href="{{ route('articoli.movimenti', $articolo) }}" class="btn btn-sm btn-outline-primary">Vedi tutti</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th class="text-center">Quantità</th>
                            <th>Magazzino</th>
                            <th>Riferimento</th>
                            <th>Registrato da</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articolo->movimenti ?? [] as $mov)
                        <tr>
                            <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $mov->tipo === 'carico' ? 'success' : ($mov->tipo === 'scarico' ? 'danger' : 'info') }}">
                                    {{ ucfirst($mov->tipo) }}
                                </span>
                            </td>
                            <td class="text-center">{{ number_format($mov->quantita, 2) }}</td>
                            <td>{{ $mov->magazzino?->nome ?? ($mov->magazzino_destinazione?->nome ?? '-') }}</td>
                            <td>{{ $mov->riferimento ?? '-' }}</td>
                            <td>{{ $mov->registratoDa?->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Nessun movimento.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
