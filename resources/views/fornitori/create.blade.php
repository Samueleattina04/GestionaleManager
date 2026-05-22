@extends('layouts.app')
@section('titolo', 'Nuovo fornitore')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nuovo fornitore</h1>
    <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<form method="POST" action="{{ route('fornitori.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Dati aziendali</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ragione sociale <span class="text-danger">*</span></label>
                        <input type="text" name="ragione_sociale" value="{{ old('ragione_sociale') }}" class="form-control @error('ragione_sociale') is-invalid @enderror" required>
                        @error('ragione_sociale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Partita IVA</label>
                            <input type="text" name="partita_iva" value="{{ old('partita_iva') }}" class="form-control @error('partita_iva') is-invalid @enderror">
                            @error('partita_iva')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Codice fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-control @error('codice_fiscale') is-invalid @enderror">
                            @error('codice_fiscale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Indirizzo</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Indirizzo</label>
                        <input type="text" name="indirizzo" value="{{ old('indirizzo') }}" class="form-control">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Città</label>
                            <input type="text" name="citta" value="{{ old('citta') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" value="{{ old('provincia') }}" class="form-control" maxlength="2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap" value="{{ old('cap') }}" class="form-control" maxlength="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paese</label>
                            <input type="text" name="paese" value="{{ old('paese', 'Italia') }}" class="form-control">
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
                            <input type="text" name="referente" value="{{ old('referente') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PEC</label>
                            <input type="email" name="pec" value="{{ old('pec') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Fatturazione elettronica</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Codice SDI</label>
                            <input type="text" name="codice_sdi" value="{{ old('codice_sdi') }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Note</strong></div>
                <div class="card-body">
                    <textarea name="note" rows="3" class="form-control">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>Impostazioni</strong></div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="attivo" value="0">
                        <input type="checkbox" name="attivo" id="attivo" class="form-check-input" value="1" {{ old('attivo', '1') ? 'checked' : '' }}>
                        <label for="attivo" class="form-check-label">Fornitore attivo</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Salva fornitore
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
