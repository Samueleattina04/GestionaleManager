@extends('layouts.app')
@section('titolo', 'I miei interventi')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">I miei interventi</h1>
    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Dashboard
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-6 col-md-3">
                <select name="stato" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="assegnato" {{ request('stato') === 'assegnato' ? 'selected' : '' }}>Assegnati</option>
                    <option value="in_corso" {{ request('stato') === 'in_corso' ? 'selected' : '' }}>In corso</option>
                    <option value="completato" {{ request('stato') === 'completato' ? 'selected' : '' }}>Completati</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="data" value="{{ request('data') }}" class="form-control">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filtra</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($interventi as $intervento)
    <div class="col-12 col-md-6">
        <div class="card h-100 border-{{ $intervento->stato_color }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">
                        <a href="{{ route('tecnico.intervento', $intervento) }}" class="text-decoration-none text-dark">
                            {{ $intervento->titolo }}
                        </a>
                    </h6>
                    <div class="d-flex gap-1">
                        <span class="badge bg-{{ $intervento->stato_color }}">{{ $intervento->stato_label }}</span>
                        <span class="badge bg-{{ $intervento->priorita === 'urgente' ? 'danger' : ($intervento->priorita === 'normale' ? 'secondary' : 'light text-dark') }}">
                            {{ ucfirst($intervento->priorita) }}
                        </span>
                    </div>
                </div>
                <p class="text-muted small mb-2">
                    <i class="bi bi-person me-1"></i>{{ $intervento->cliente?->nome_completo }}
                </p>
                @if($intervento->indirizzo_intervento)
                <p class="text-muted small mb-2">
                    <i class="bi bi-geo-alt me-1"></i>{{ $intervento->indirizzo_intervento }}
                </p>
                @endif
                <p class="text-muted small mb-0">
                    <i class="bi bi-calendar me-1"></i>
                    {{ $intervento->data_pianificata?->format('d/m/Y H:i') ?? 'Non pianificato' }}
                </p>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="{{ route('tecnico.intervento', $intervento) }}" class="btn btn-sm btn-primary w-100">
                    Apri intervento <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                Nessun intervento assegnato.
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($interventi->hasPages())
<div class="mt-4">{{ $interventi->withQueryString()->links() }}</div>
@endif
@endsection
