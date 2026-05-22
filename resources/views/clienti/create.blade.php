@extends('layouts.app')
@section('titolo', 'Nuovo Cliente')

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('clienti.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Clienti</a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Nuovo Cliente</span>
</div>

<div class="card" style="max-width:800px">
    <div class="card-body">
        <form action="{{ route('clienti.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="azienda" {{ old('tipo') === 'azienda' ? 'selected' : '' }}>Azienda</option>
                        <option value="privato" {{ old('tipo') === 'privato' ? 'selected' : '' }}>Privato</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Ragione Sociale / Nome *</label>
                    <input type="text" name="ragione_sociale" class="form-control" value="{{ old('ragione_sociale') }}" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Partita IVA</label>
                    <input type="text" name="partita_iva" class="form-control" value="{{ old('partita_iva') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Codice Fiscale</label>
                    <input type="text" name="codice_fiscale" class="form-control" value="{{ old('codice_fiscale') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Indirizzo</label>
                    <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo') }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Città</label>
                    <input type="text" name="citta" class="form-control" value="{{ old('citta') }}">
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-medium">Provincia</label>
                    <input type="text" name="provincia" class="form-control" maxlength="2" value="{{ old('provincia') }}">
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-medium">CAP</label>
                    <input type="text" name="cap" class="form-control" value="{{ old('cap') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Referente</label>
                    <input type="text" name="referente" class="form-control" value="{{ old('referente') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Telefono</label>
                    <input type="tel" name="telefono" class="form-control" value="{{ old('telefono') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">PEC</label>
                    <input type="email" name="pec" class="form-control" value="{{ old('pec') }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Codice SDI</label>
                    <input type="text" name="codice_sdi" class="form-control" maxlength="7" value="{{ old('codice_sdi') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Note</label>
                    <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Salva Cliente</button>
                <a href="{{ route('clienti.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
