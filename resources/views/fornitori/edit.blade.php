@extends('layouts.app')
@section('titolo', 'Modifica fornitore')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Modifica: {{ $fornitore->ragione_sociale }}</h1>
    <a href="{{ route('fornitori.show', $fornitore) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<form method="POST" action="{{ route('fornitori.update', $fornitore) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Dati aziendali</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ragione sociale <span class="text-danger">*</span></label>
                        <input type="text" name="ragione_sociale" value="{{ old('ragione_sociale', $fornitore->ragione_sociale) }}" class="form-control @error('ragione_sociale') is-invalid @enderror" required>
                        @error('ragione_sociale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Partita IVA</label>
                            <input type="text" name="partita_iva" value="{{ old('partita_iva', $fornitore->partita_iva) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Codice fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $fornitore->codice_fiscale) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Indirizzo</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Indirizzo</label>
                        <input type="text" name="indirizzo" value="{{ old('indirizzo', $fornitore->indirizzo) }}" class="form-control">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Città</label>
                            <input type="text" name="citta" value="{{ old('citta', $fornitore->citta) }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" value="{{ old('provincia', $fornitore->provincia) }}" class="form-control" maxlength="2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap" value="{{ old('cap', $fornitore->cap) }}" class="form-control" maxlength="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paese</label>
                            <input type="text" name="paese" value="{{ old('paese', $fornitore->paese) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Contatti</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Referente</label>
                            <input type="text" name="referente" value="{{ old('referente', $fornitore->referente) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $fornitore->telefono) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $fornitore->email) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PEC</label>
                            <input type="email" name="pec" value="{{ old('pec', $fornitore->pec) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Codice SDI</label>
                            <input type="text" name="codice_sdi" value="{{ old('codice_sdi', $fornitore->codice_sdi) }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Note</strong></div>
                <div class="card-body">
                    <textarea name="note" rows="3" class="form-control">{{ old('note', $fornitore->note) }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>Impostazioni</strong></div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="attivo" value="0">
                        <input type="checkbox" name="attivo" id="attivo" class="form-check-input" value="1" {{ old('attivo', $fornitore->attivo) ? 'checked' : '' }}>
                        <label for="attivo" class="form-check-label">Fornitore attivo</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Aggiorna fornitore
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
