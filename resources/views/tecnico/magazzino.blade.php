@extends('layouts.app')
@section('titolo', 'Magazzino')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Magazzino</h1>
    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Dashboard
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="ricerca" value="{{ request('ricerca') }}" class="form-control" placeholder="Cerca articolo per nome o codice...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Cerca</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3" x-data="{ modalArticolo: null }">
    @forelse($articoli as $articolo)
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-0"><code>{{ $articolo->codice }}</code></p>
                        <h6 class="mb-1">{{ $articolo->nome }}</h6>
                        <p class="text-muted small mb-0">{{ $articolo->categoria?->nome }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ ($articolo->giacenza?->quantita ?? 0) < ($articolo->scorta_minima ?? 0) ? 'danger' : 'success' }} fs-6">
                            {{ number_format($articolo->giacenza?->quantita ?? 0, 2) }}
                        </span>
                        <small class="d-block text-muted">{{ strtoupper($articolo->unita_misura) }}</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button type="button" class="btn btn-sm btn-outline-primary w-100"
                    data-bs-toggle="modal" data-bs-target="#modalUso"
                    data-id="{{ $articolo->id }}" data-nome="{{ $articolo->nome }}"
                    data-um="{{ $articolo->unita_misura }}">
                    <i class="bi bi-box-arrow-up me-1"></i>Registra utilizzo
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                Nessun articolo trovato.
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($articoli->hasPages())
<div class="mt-4">{{ $articoli->withQueryString()->links() }}</div>
@endif

<!-- Modal utilizzo -->
<div class="modal fade" id="modalUso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tecnico.usa-articolo') }}">
                @csrf
                <input type="hidden" name="articolo_id" id="usoArticoloId">
                <div class="modal-header">
                    <h5 class="modal-title">Registra utilizzo: <span id="usoArticoloNome"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quantità <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantita" class="form-control" min="0.01" step="0.01" required>
                            <span class="input-group-text" id="usoUm">pz</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intervento correlato</label>
                        <select name="intervento_id" class="form-select">
                            <option value="">Nessuno</option>
                            @foreach(auth()->user()->interventi()->whereIn('stato', ['assegnato','in_corso'])->get() as $i)
                            <option value="{{ $i->id }}">{{ $i->numero }} - {{ $i->titolo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Conferma utilizzo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('modalUso').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('usoArticoloId').value = btn.dataset.id;
    document.getElementById('usoArticoloNome').textContent = btn.dataset.nome;
    document.getElementById('usoUm').textContent = btn.dataset.um;
});
</script>
@endsection
