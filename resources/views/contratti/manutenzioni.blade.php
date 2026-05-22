@extends('layouts.app')
@section('titolo', 'Manutenzioni – ' . $contratto->numero)

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('contratti.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Contratti</a>
    <span class="text-muted">/</span>
    <a href="{{ route('contratti.show', $contratto) }}" class="text-muted text-decoration-none">{{ $contratto->numero }}</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Manutenzioni</span>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <h6 class="fw-bold mb-1">{{ $contratto->titolo }}</h6>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small">{{ $contratto->numero }}</span>
                    <span class="text-muted small">•</span>
                    <span class="text-muted small">{{ $contratto->cliente->ragione_sociale }}</span>
                    @php
                        $badgeStato = match($contratto->stato) {
                            'attivo'    => 'success',
                            'scaduto'   => 'danger',
                            'bozza'     => 'secondary',
                            'annullato' => 'dark',
                            default     => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeStato }}">{{ ucfirst($contratto->stato) }}</span>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="text-muted small">Freq. Manutenzione:</span>
                <span class="fw-medium ms-1">{{ ucfirst($contratto->frequenza_manutenzione ?? 'N/D') }}</span>
                @if($contratto->data_fine)
                    <span class="text-muted small ms-3">Scadenza:</span>
                    <span class="fw-medium ms-1">{{ $contratto->data_fine->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $totale      = $manutenzioni->total();
    $completate  = $manutenzioni->getCollection()->where('stato', 'completata')->count();
    $inScadenza  = $manutenzioni->getCollection()->filter(fn($m) =>
        $m->stato !== 'completata' &&
        $m->stato !== 'annullata' &&
        $m->data_pianificata->diffInDays(now(), false) >= -7 &&
        $m->data_pianificata->isFuture()
    )->count();
@endphp

<div class="row g-3 mb-3">
    <div class="col-sm-4">
        <div class="card text-center border-0 bg-light">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">{{ $totale }}</div>
                <div class="text-muted small">Totale</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center border-0 bg-success bg-opacity-10">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-success">{{ $completate }}</div>
                <div class="text-muted small">Completate (pagina)</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center border-0 bg-warning bg-opacity-10">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-warning">{{ $inScadenza }}</div>
                <div class="text-muted small">In Scadenza (pagina)</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('contratti.manutenzioni', $contratto) }}" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label fw-medium mb-1 small">Stato</label>
                <select name="stato" class="form-select form-select-sm">
                    <option value="">Tutti gli stati</option>
                    <option value="pianificata" {{ request('stato') === 'pianificata' ? 'selected' : '' }}>Pianificata</option>
                    <option value="in_corso" {{ request('stato') === 'in_corso' ? 'selected' : '' }}>In Corso</option>
                    <option value="completata" {{ request('stato') === 'completata' ? 'selected' : '' }}>Completata</option>
                    <option value="annullata" {{ request('stato') === 'annullata' ? 'selected' : '' }}>Annullata</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label fw-medium mb-1 small">Mese</label>
                <input type="month" name="mese" class="form-control form-control-sm" value="{{ request('mese') }}">
            </div>
            <div class="col-sm-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Filtra
                </button>
                <a href="{{ route('contratti.manutenzioni', $contratto) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data Pianificata</th>
                    <th>Stato</th>
                    <th>Tecnico</th>
                    <th>Note</th>
                    <th class="text-center">Completata</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeManutenzione = [
                        'pianificata' => 'primary',
                        'in_corso'    => 'warning',
                        'completata'  => 'success',
                        'annullata'   => 'secondary',
                    ];
                @endphp
                @forelse($manutenzioni as $manutenzione)
                <tr>
                    <td>
                        {{ $manutenzione->data_pianificata->format('d/m/Y') }}
                        @if($manutenzione->stato !== 'completata' && $manutenzione->stato !== 'annullata' && $manutenzione->data_pianificata->isPast())
                            <i class="bi bi-exclamation-circle text-danger ms-1" title="Scaduta"></i>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $badgeManutenzione[$manutenzione->stato] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $manutenzione->stato)) }}
                        </span>
                    </td>
                    <td>{{ $manutenzione->tecnico->name ?? '—' }}</td>
                    <td class="text-muted small">{{ $manutenzione->note ?? '—' }}</td>
                    <td class="text-center">
                        <form action="{{ route('manutenzioni.update', $manutenzione) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="stato" value="{{ $manutenzione->stato === 'completata' ? 'pianificata' : 'completata' }}">
                            <input type="hidden" name="contratto_id" value="{{ $contratto->id }}">
                            <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent">
                                <i class="bi bi-{{ $manutenzione->stato === 'completata' ? 'check-circle-fill text-success' : 'circle text-muted' }} fs-5"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>Nessuna manutenzione trovata
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($manutenzioni->hasPages())
    <div class="card-footer">
        {{ $manutenzioni->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
