@extends('layouts.app')
@section('titolo', 'Nuovo Intervento')

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('interventi.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Interventi</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Nuovo Intervento</span>
</div>

<div class="card" style="max-width:900px">
    <div class="card-body">
        <form action="{{ route('interventi.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label fw-medium">Cliente *</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">— Seleziona cliente —</option>
                        @foreach($clienti as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id', request('cliente_id')) == $c->id ? 'selected' : '' }}>
                            {{ $c->ragione_sociale }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Priorità *</label>
                    <select name="priorita" class="form-select" required>
                        <option value="normale" {{ old('priorita') === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="urgente" {{ old('priorita') === 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                        <option value="bassa" {{ old('priorita') === 'bassa' ? 'selected' : '' }}>Bassa</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Titolo *</label>
                    <input type="text" name="titolo" class="form-control" value="{{ old('titolo') }}" required placeholder="Breve descrizione dell'intervento">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Descrizione</label>
                    <textarea name="descrizione" class="form-control" rows="4" placeholder="Descrizione dettagliata del problema o dell'attività...">{{ old('descrizione') }}</textarea>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Tecnico assegnato</label>
                    <select name="tecnico_id" class="form-select">
                        <option value="">— Da assegnare —</option>
                        @foreach($tecnici as $t)
                        <option value="{{ $t->id }}" {{ old('tecnico_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Data e ora pianificata</label>
                    <input type="datetime-local" name="data_pianificata" class="form-control" value="{{ old('data_pianificata') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Indirizzo intervento</label>
                    <input type="text" name="indirizzo_intervento" class="form-control" value="{{ old('indirizzo_intervento') }}" placeholder="Lascia vuoto per usare l'indirizzo del cliente">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Note interne</label>
                    <textarea name="note_interne" class="form-control" rows="3" placeholder="Note visibili solo allo staff...">{{ old('note_interne') }}</textarea>
                </div>

                <!-- Checklist iniziale -->
                <div class="col-12">
                    <label class="form-label fw-medium">Checklist attività</label>
                    <div id="checklistContainer">
                        <div class="input-group mb-2">
                            <input type="text" name="checklist[]" class="form-control" placeholder="Prima voce da completare...">
                            <button type="button" class="btn btn-outline-secondary" onclick="rimuoviVoce(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="aggiungiVoce()">
                        <i class="bi bi-plus me-1"></i>Aggiungi voce
                    </button>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Crea Intervento</button>
                <a href="{{ route('interventi.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function aggiungiVoce() {
    const c = document.getElementById('checklistContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" name="checklist[]" class="form-control" placeholder="Voce da completare...">
        <button type="button" class="btn btn-outline-secondary" onclick="rimuoviVoce(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    c.appendChild(div);
}
function rimuoviVoce(btn) {
    btn.parentElement.remove();
}
</script>
@endpush
@endsection
