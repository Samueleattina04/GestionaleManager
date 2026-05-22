@extends('layouts.app')
@section('titolo', 'Contratto ' . $contratto->numero)

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('contratti.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Contratti</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">{{ $contratto->numero }}</span>
</div>

@php
    $badgeStato = match($contratto->stato) {
        'attivo'    => 'success',
        'scaduto'   => 'danger',
        'bozza'     => 'secondary',
        'annullato' => 'dark',
        default     => 'secondary',
    };
@endphp

<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">{{ $contratto->titolo }}</h4>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted fw-medium">{{ $contratto->numero }}</span>
            <span class="badge bg-{{ $badgeStato }}">{{ ucfirst($contratto->stato) }}</span>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($contratto->stato === 'attivo')
        <form action="{{ route('contratti.genera-manutenzioni', $contratto) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar-plus me-1"></i>Genera Manutenzioni
            </button>
        </form>
        @endif
        @can('modifica_contratti')
        <a href="{{ route('contratti.edit', $contratto) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        @endcan
        <a href="{{ route('contratti.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Cliente
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1">{{ $contratto->cliente->ragione_sociale }}</h6>
                @if($contratto->cliente->partita_iva)
                    <div class="text-muted small mb-1">P.IVA: {{ $contratto->cliente->partita_iva }}</div>
                @endif
                @if($contratto->cliente->indirizzo)
                    <div class="text-muted small mb-1">
                        <i class="bi bi-geo-alt me-1"></i>{{ $contratto->cliente->indirizzo }}
                        @if($contratto->cliente->citta)
                            – {{ $contratto->cliente->citta }}
                            @if($contratto->cliente->provincia) ({{ $contratto->cliente->provincia }}) @endif
                        @endif
                    </div>
                @endif
                @if($contratto->cliente->telefono)
                    <div class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i>{{ $contratto->cliente->telefono }}</div>
                @endif
                @if($contratto->cliente->email)
                    <div class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i>{{ $contratto->cliente->email }}</div>
                @endif
                @if($contratto->cliente->referente)
                    <div class="text-muted small"><i class="bi bi-person-badge me-1"></i>Referente: {{ $contratto->cliente->referente }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-file-earmark-check me-2"></i>Dettagli Contratto
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.9rem">
                    <dt class="col-sm-5 text-muted fw-normal">Tipo</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-light text-dark border">{{ ucfirst($contratto->tipo) }}</span>
                    </dd>

                    <dt class="col-sm-5 text-muted fw-normal">Periodo</dt>
                    <dd class="col-sm-7">
                        {{ $contratto->data_inizio->format('d/m/Y') }}
                        @if($contratto->data_fine)
                            – {{ $contratto->data_fine->format('d/m/Y') }}
                        @else
                            – <span class="text-muted">Nessuna scadenza</span>
                        @endif
                    </dd>

                    <dt class="col-sm-5 text-muted fw-normal">Importo</dt>
                    <dd class="col-sm-7 fw-semibold">€ {{ number_format($contratto->importo, 2, ',', '.') }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Freq. Fatturazione</dt>
                    <dd class="col-sm-7">{{ ucfirst($contratto->frequenza_fatturazione) }}</dd>

                    @if($contratto->frequenza_manutenzione)
                    <dt class="col-sm-5 text-muted fw-normal">Freq. Manutenzione</dt>
                    <dd class="col-sm-7">{{ ucfirst($contratto->frequenza_manutenzione) }}</dd>
                    @endif

                    <dt class="col-sm-5 text-muted fw-normal">Rinnovo Automatico</dt>
                    <dd class="col-sm-7">
                        @if($contratto->rinnovo_automatico)
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Sì</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-x-circle me-1"></i>No</span>
                        @endif
                    </dd>

                    @if($contratto->descrizione)
                    <dt class="col-sm-5 text-muted fw-normal">Descrizione</dt>
                    <dd class="col-sm-7">{{ $contratto->descrizione }}</dd>
                    @endif

                    @if($contratto->note)
                    <dt class="col-sm-5 text-muted fw-normal">Note</dt>
                    <dd class="col-sm-7">{{ $contratto->note }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check me-2"></i>Manutenzioni Programmate</span>
        <span class="badge bg-secondary">{{ $contratto->manutenzioni->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data Pianificata</th>
                    <th>Stato</th>
                    <th>Tecnico</th>
                    <th>Note</th>
                    <th class="text-end">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeManutenzione = [
                        'pianificata'  => 'primary',
                        'in_corso'     => 'warning',
                        'completata'   => 'success',
                        'annullata'    => 'secondary',
                    ];
                @endphp
                @forelse($contratto->manutenzioni as $manutenzione)
                <tr>
                    <td>{{ $manutenzione->data_pianificata->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $badgeManutenzione[$manutenzione->stato] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $manutenzione->stato)) }}
                        </span>
                    </td>
                    <td>{{ $manutenzione->tecnico->name ?? '—' }}</td>
                    <td>{{ $manutenzione->note ?? '—' }}</td>
                    <td class="text-end">
                        @can('modifica_interventi')
                        <a href="{{ route('interventi.show', $manutenzione) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>Nessuna manutenzione programmata
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
