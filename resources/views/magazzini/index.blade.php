@extends('layouts.app')
@section('titolo', 'Magazzini')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Magazzini</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('magazzini.sotto-scorta') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-exclamation-triangle me-1"></i>Articoli sotto scorta
        </a>
        @can('modifica_magazzino')
        <a href="{{ route('magazzini.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nuovo magazzino
        </a>
        @endcan
    </div>
</div>

<div class="row g-3">
    @forelse($magazzini as $magazzino)
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-0">
                            {{ $magazzino->nome }}
                            @if($magazzino->principale)
                                <span class="badge bg-primary ms-1" style="font-size:0.65rem">Principale</span>
                            @endif
                            @if(!$magazzino->attivo)
                                <span class="badge bg-secondary ms-1" style="font-size:0.65rem">Inattivo</span>
                            @endif
                        </h6>
                        @if($magazzino->indirizzo)
                        <small class="text-muted">
                            <i class="bi bi-geo-alt me-1"></i>{{ $magazzino->indirizzo }}{{ $magazzino->citta ? ', '.$magazzino->citta : '' }}{{ $magazzino->provincia ? ' ('.$magazzino->provincia.')' : '' }}
                        </small>
                        @endif
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">{{ number_format($magazzino->giacenze_sum_valore ?? 0, 2, ',', '.') }} €</div>
                        <small class="text-muted">valore totale</small>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="bg-light rounded p-2 text-center">
                            <div class="fw-semibold">{{ $magazzino->giacenze_count ?? 0 }}</div>
                            <small class="text-muted">Articoli</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded p-2 text-center">
                            <div class="fw-semibold">{{ $magazzino->responsabile?->name ?? '—' }}</div>
                            <small class="text-muted">Responsabile</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-1 flex-wrap">
                    <a href="{{ route('magazzini.show', $magazzino) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i>Dettaglio
                    </a>
                    <a href="{{ route('magazzini.inventario', $magazzino) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-clipboard-check me-1"></i>Inventario
                    </a>
                    @can('modifica_magazzino')
                    <a href="{{ route('magazzini.edit', $magazzino) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    <form action="{{ route('magazzini.destroy', $magazzino) }}" method="POST"
                          onsubmit="return confirm('Eliminare il magazzino {{ addslashes($magazzino->nome) }}? Questa operazione è irreversibile.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-building fs-1 d-block mb-2"></i>
                <p class="mb-0">Nessun magazzino configurato</p>
                @can('modifica_magazzino')
                <a href="{{ route('magazzini.create') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-plus-lg me-1"></i>Crea il primo magazzino
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($magazzini->hasPages())
<div class="mt-3">{{ $magazzini->links() }}</div>
@endif
@endsection
