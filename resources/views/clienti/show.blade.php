@extends('layouts.app')
@section('titolo', $cliente->ragione_sociale)

@section('contenuto')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('clienti.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Clienti</a>
        <span class="text-muted">/</span>
        <span class="fw-semibold">{{ $cliente->ragione_sociale }}</span>
    </div>
    @can('modifica_clienti')
    <div class="d-flex gap-2">
        <a href="{{ route('clienti.edit', $cliente) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <form action="{{ route('clienti.destroy', $cliente) }}" method="POST" onsubmit="return confirm('Eliminare questo cliente?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Elimina</button>
        </form>
    </div>
    @endcan
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:64px;height:64px">
                        <i class="bi bi-{{ $cliente->tipo === 'azienda' ? 'building' : 'person' }} fs-2 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $cliente->ragione_sociale }}</h5>
                    <span class="badge bg-light text-dark">{{ $cliente->codice }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @if($cliente->partita_iva)
                    <div class="list-group-item px-0"><small class="text-muted d-block">P.IVA</small>{{ $cliente->partita_iva }}</div>
                    @endif
                    @if($cliente->codice_fiscale)
                    <div class="list-group-item px-0"><small class="text-muted d-block">C.F.</small>{{ $cliente->codice_fiscale }}</div>
                    @endif
                    @if($cliente->referente)
                    <div class="list-group-item px-0"><small class="text-muted d-block">Referente</small>{{ $cliente->referente }}</div>
                    @endif
                    @if($cliente->telefono)
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block">Telefono</small>
                        <a href="tel:{{ $cliente->telefono }}">{{ $cliente->telefono }}</a>
                    </div>
                    @endif
                    @if($cliente->email)
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block">Email</small>
                        <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                    </div>
                    @endif
                    @if($cliente->indirizzo)
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block">Indirizzo</small>
                        {{ $cliente->indirizzo_completo }}
                        <br>
                        <a href="https://maps.google.com/?q={{ urlencode($cliente->indirizzo_completo) }}" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm mt-1">
                            <i class="bi bi-map me-1"></i>Mappa
                        </a>
                    </div>
                    @endif
                    @if($cliente->pec)
                    <div class="list-group-item px-0"><small class="text-muted d-block">PEC</small>{{ $cliente->pec }}</div>
                    @endif
                    @if($cliente->codice_sdi)
                    <div class="list-group-item px-0"><small class="text-muted d-block">Codice SDI</small>{{ $cliente->codice_sdi }}</div>
                    @endif
                </div>
                @if($cliente->note)
                <div class="mt-3 p-2 bg-light rounded">
                    <small class="text-muted d-block fw-medium">Note</small>
                    <small>{{ $cliente->note }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Statistiche -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="card text-center p-2">
                    <div class="fw-bold fs-4">{{ $cliente->interventi->count() }}</div>
                    <small class="text-muted">Interventi</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center p-2">
                    <div class="fw-bold fs-4">{{ $cliente->fatture->count() }}</div>
                    <small class="text-muted">Fatture</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center p-2">
                    <div class="fw-bold fs-4">{{ $cliente->contratti->count() }}</div>
                    <small class="text-muted">Contratti</small>
                </div>
            </div>
        </div>

        <!-- Ultimi interventi -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Ultimi Interventi</span>
                @can('crea_interventi')
                <a href="{{ route('interventi.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1"></i>Nuovo
                </a>
                @endcan
            </div>
            <div class="list-group list-group-flush">
                @forelse($cliente->interventi->take(5) as $i)
                <a href="{{ route('interventi.show', $i) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-medium">{{ $i->titolo }}</div>
                        <small class="text-muted">{{ $i->data_pianificata?->format('d/m/Y') ?? $i->created_at->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge badge-stato-{{ $i->stato }}">{{ $i->stato_label }}</span>
                </a>
                @empty
                <div class="list-group-item text-muted text-center py-3">Nessun intervento</div>
                @endforelse
            </div>
        </div>

        <!-- Fatture recenti -->
        <div class="card">
            <div class="card-header fw-semibold">Fatture Recenti</div>
            <div class="list-group list-group-flush">
                @forelse($cliente->fatture->take(5) as $f)
                <a href="{{ route('fatture.show', $f) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-medium">Fattura n. {{ $f->numero }}</div>
                        <small class="text-muted">{{ $f->data->format('d/m/Y') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">€ {{ number_format($f->totale, 2, ',', '.') }}</div>
                        <span class="badge bg-{{ $f->stato === 'pagata' ? 'success' : ($f->stato === 'scaduta' ? 'danger' : 'primary') }}">{{ $f->stato_label }}</span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-muted text-center py-3">Nessuna fattura</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
