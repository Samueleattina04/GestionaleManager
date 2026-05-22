@extends('layouts.app')
@section('titolo', 'Modifica Intervento')

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('interventi.show', $intervento) }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ $intervento->numero }}</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Modifica</span>
</div>

<div class="card" style="max-width:900px">
    <div class="card-body">
        <form action="{{ route('interventi.update', $intervento) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label fw-medium">Cliente *</label>
                    <select name="cliente_id" class="form-select" required>
                        @foreach($clienti as $c)
                        <option value="{{ $c->id }}" {{ $intervento->cliente_id == $c->id ? 'selected' : '' }}>{{ $c->ragione_sociale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Priorità *</label>
                    <select name="priorita" class="form-select" required>
                        <option value="normale" {{ $intervento->priorita === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="urgente" {{ $intervento->priorita === 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                        <option value="bassa" {{ $intervento->priorita === 'bassa' ? 'selected' : '' }}>Bassa</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Titolo *</label>
                    <input type="text" name="titolo" class="form-control" value="{{ old('titolo', $intervento->titolo) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Descrizione</label>
                    <textarea name="descrizione" class="form-control" rows="4">{{ old('descrizione', $intervento->descrizione) }}</textarea>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Tecnico</label>
                    <select name="tecnico_id" class="form-select">
                        <option value="">— Da assegnare —</option>
                        @foreach($tecnici as $t)
                        <option value="{{ $t->id }}" {{ $intervento->tecnico_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Data e ora pianificata</label>
                    <input type="datetime-local" name="data_pianificata" class="form-control" value="{{ old('data_pianificata', $intervento->data_pianificata?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Note interne</label>
                    <textarea name="note_interne" class="form-control" rows="3">{{ old('note_interne', $intervento->note_interne) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Note per il cliente</label>
                    <textarea name="note_cliente" class="form-control" rows="3">{{ old('note_cliente', $intervento->note_cliente) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Salva Modifiche</button>
                <a href="{{ route('interventi.show', $intervento) }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
