@extends('layouts.app')
@section('titolo', $fornitore->ragione_sociale)
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $fornitore->ragione_sociale }}</h1>
    <div>
        <a href="{{ route('fornitori.edit', $fornitore) }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Dati aziendali</strong>
                @if($fornitore->attivo)
                    <span class="badge bg-success">Attivo</span>
                @else
                    <span class="badge bg-secondary">Inattivo</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Codice</dt>
                    <dd class="col-7"><code>{{ $fornitore->codice }}</code></dd>
                    <dt class="col-5">P. IVA</dt>
                    <dd class="col-7">{{ $fornitore->partita_iva ?? '-' }}</dd>
                    <dt class="col-5">Cod. fiscale</dt>
                    <dd class="col-7">{{ $fornitore->codice_fiscale ?? '-' }}</dd>
                    <dt class="col-5">Indirizzo</dt>
                    <dd class="col-7">{{ $fornitore->indirizzo ?? '-' }}</dd>
                    <dt class="col-5">Città</dt>
                    <dd class="col-7">{{ $fornitore->citta }}{{ $fornitore->provincia ? ' ('.$fornitore->provincia.')' : '' }}</dd>
                    <dt class="col-5">CAP</dt>
                    <dd class="col-7">{{ $fornitore->cap ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Contatti</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Referente</dt>
                    <dd class="col-7">{{ $fornitore->referente ?? '-' }}</dd>
                    <dt class="col-5">Telefono</dt>
                    <dd class="col-7">
                        @if($fornitore->telefono)
                            <a href="tel:{{ $fornitore->telefono }}">{{ $fornitore->telefono }}</a>
                        @else -
                        @endif
                    </dd>
                    <dt class="col-5">Email</dt>
                    <dd class="col-7">
                        @if($fornitore->email)
                            <a href="mailto:{{ $fornitore->email }}">{{ $fornitore->email }}</a>
                        @else -
                        @endif
                    </dd>
                    <dt class="col-5">PEC</dt>
                    <dd class="col-7">{{ $fornitore->pec ?? '-' }}</dd>
                    <dt class="col-5">Cod. SDI</dt>
                    <dd class="col-7">{{ $fornitore->codice_sdi ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        @if($fornitore->note)
        <div class="card">
            <div class="card-header"><strong>Note</strong></div>
            <div class="card-body">
                <p class="mb-0">{{ $fornitore->note }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Ultime fatture di acquisto</strong>
                <a href="{{ route('fatture.create') }}?fornitore_id={{ $fornitore->id }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuova fattura
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Numero</th>
                            <th>Data</th>
                            <th>Importo</th>
                            <th>Stato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fornitore->fatture()->latest()->take(10)->get() as $fattura)
                        <tr>
                            <td>{{ $fattura->numero }}</td>
                            <td>{{ $fattura->data_emissione?->format('d/m/Y') }}</td>
                            <td>€ {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $fattura->stato === 'pagata' ? 'success' : ($fattura->stato === 'scaduta' ? 'danger' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $fattura->stato)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('fatture.show', $fattura) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Nessuna fattura.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
