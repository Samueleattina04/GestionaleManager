@extends('layouts.app')
@section('titolo', 'Storico ' . $cliente->ragione_sociale)

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('clienti.show', $cliente) }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ $cliente->ragione_sociale }}</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Storico Completo</span>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header fw-semibold">Tutti gli Interventi ({{ $cliente->interventi->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($cliente->interventi as $i)
                <a href="{{ route('interventi.show', $i) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-medium">{{ $i->titolo }}</div>
                        <small class="text-muted">{{ $i->created_at->format('d/m/Y') }} — {{ $i->tecnico?->name ?? '—' }}</small>
                    </div>
                    <span class="badge badge-stato-{{ $i->stato }}">{{ $i->stato_label }}</span>
                </a>
                @empty
                <div class="list-group-item text-muted text-center py-3">Nessun intervento</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header fw-semibold">Tutte le Fatture ({{ $cliente->fatture->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($cliente->fatture as $f)
                <a href="{{ route('fatture.show', $f) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-medium">Fattura {{ $f->numero }}/{{ $f->anno }}</div>
                        <small class="text-muted">{{ $f->data->format('d/m/Y') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">€ {{ number_format($f->totale, 2, ',', '.') }}</div>
                        <span class="badge bg-{{ $f->stato === 'pagata' ? 'success' : 'warning' }}">{{ $f->stato_label }}</span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-muted text-center py-3">Nessuna fattura</div>
                @endforelse
            </div>
        </div>
        <div class="card">
            <div class="card-header fw-semibold">Contratti ({{ $cliente->contratti->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($cliente->contratti as $c)
                <a href="{{ route('contratti.show', $c) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <div>
                        <div class="fw-medium">{{ $c->titolo }}</div>
                        <small class="text-muted">{{ $c->data_inizio->format('d/m/Y') }} → {{ $c->data_fine->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $c->stato === 'attivo' ? 'success' : 'secondary' }}">{{ ucfirst($c->stato) }}</span>
                </a>
                @empty
                <div class="list-group-item text-muted text-center py-3">Nessun contratto</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
