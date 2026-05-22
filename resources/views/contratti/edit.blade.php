@extends('layouts.app')
@section('titolo', 'Modifica Contratto')

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('contratti.show', $contratto) }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ $contratto->numero }}</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Modifica</span>
</div>

<div class="card" style="max-width:900px">
    <div class="card-header">
        <i class="bi bi-pencil-square me-2"></i>Modifica Contratto
    </div>
    <div class="card-body">
        <form action="{{ route('contratti.update', $contratto) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-medium">Cliente *</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">-- Seleziona cliente --</option>
                        @foreach($clienti as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $contratto->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->ragione_sociale }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Titolo *</label>
                    <input type="text" name="titolo" class="form-control" value="{{ old('titolo', $contratto->titolo) }}" required>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">-- Seleziona tipo --</option>
                        <option value="assistenza" {{ old('tipo', $contratto->tipo) === 'assistenza' ? 'selected' : '' }}>Assistenza</option>
                        <option value="manutenzione" {{ old('tipo', $contratto->tipo) === 'manutenzione' ? 'selected' : '' }}>Manutenzione</option>
                        <option value="noleggio" {{ old('tipo', $contratto->tipo) === 'noleggio' ? 'selected' : '' }}>Noleggio</option>
                        <option value="altro" {{ old('tipo', $contratto->tipo) === 'altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Stato *</label>
                    <select name="stato" class="form-select" required>
                        <option value="">-- Seleziona stato --</option>
                        <option value="bozza" {{ old('stato', $contratto->stato) === 'bozza' ? 'selected' : '' }}>Bozza</option>
                        <option value="attivo" {{ old('stato', $contratto->stato) === 'attivo' ? 'selected' : '' }}>Attivo</option>
                        <option value="scaduto" {{ old('stato', $contratto->stato) === 'scaduto' ? 'selected' : '' }}>Scaduto</option>
                        <option value="annullato" {{ old('stato', $contratto->stato) === 'annullato' ? 'selected' : '' }}>Annullato</option>
                    </select>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Data Inizio *</label>
                    <input type="date" name="data_inizio" class="form-control" value="{{ old('data_inizio', $contratto->data_inizio?->format('Y-m-d')) }}" required>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Data Fine</label>
                    <input type="date" name="data_fine" class="form-control" value="{{ old('data_fine', $contratto->data_fine?->format('Y-m-d')) }}">
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Importo (€) *</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="importo" class="form-control" value="{{ old('importo', $contratto->importo) }}" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Frequenza Fatturazione *</label>
                    <select name="frequenza_fatturazione" class="form-select" required>
                        <option value="">-- Seleziona frequenza --</option>
                        <option value="mensile" {{ old('frequenza_fatturazione', $contratto->frequenza_fatturazione) === 'mensile' ? 'selected' : '' }}>Mensile</option>
                        <option value="bimestrale" {{ old('frequenza_fatturazione', $contratto->frequenza_fatturazione) === 'bimestrale' ? 'selected' : '' }}>Bimestrale</option>
                        <option value="trimestrale" {{ old('frequenza_fatturazione', $contratto->frequenza_fatturazione) === 'trimestrale' ? 'selected' : '' }}>Trimestrale</option>
                        <option value="semestrale" {{ old('frequenza_fatturazione', $contratto->frequenza_fatturazione) === 'semestrale' ? 'selected' : '' }}>Semestrale</option>
                        <option value="annuale" {{ old('frequenza_fatturazione', $contratto->frequenza_fatturazione) === 'annuale' ? 'selected' : '' }}>Annuale</option>
                    </select>
                </div>

                <div class="col-sm-6">
                    <label class="form-label fw-medium">Frequenza Manutenzione</label>
                    <select name="frequenza_manutenzione" class="form-select">
                        <option value="">-- Nessuna --</option>
                        <option value="settimanale" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'settimanale' ? 'selected' : '' }}>Settimanale</option>
                        <option value="mensile" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'mensile' ? 'selected' : '' }}>Mensile</option>
                        <option value="bimestrale" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'bimestrale' ? 'selected' : '' }}>Bimestrale</option>
                        <option value="trimestrale" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'trimestrale' ? 'selected' : '' }}>Trimestrale</option>
                        <option value="semestrale" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'semestrale' ? 'selected' : '' }}>Semestrale</option>
                        <option value="annuale" {{ old('frequenza_manutenzione', $contratto->frequenza_manutenzione) === 'annuale' ? 'selected' : '' }}>Annuale</option>
                    </select>
                </div>

                <div class="col-sm-6 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="rinnovo_automatico" id="rinnovo_automatico" value="1"
                            {{ old('rinnovo_automatico', $contratto->rinnovo_automatico) ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="rinnovo_automatico">
                            Rinnovo Automatico
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Descrizione</label>
                    <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione', $contratto->descrizione) }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Note</label>
                    <textarea name="note" class="form-control" rows="3">{{ old('note', $contratto->note) }}</textarea>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Salva Modifiche
                </button>
                <a href="{{ route('contratti.show', $contratto) }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
