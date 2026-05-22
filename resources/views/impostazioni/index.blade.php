@extends('layouts.app')
@section('titolo', 'Impostazioni azienda')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Impostazioni azienda</h1>
    <a href="{{ route('impostazioni.utenti') }}" class="btn btn-outline-primary">
        <i class="bi bi-people me-1"></i>Gestione utenti
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('impostazioni.aggiorna') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <ul class="nav nav-tabs mb-4" id="settingsTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#dati">Dati aziendali</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#indirizzo">Indirizzo</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#fatturazione">Fatturazione</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#personalizzazione">Personalizzazione</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="dati">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ragione sociale <span class="text-danger">*</span></label>
                            <input type="text" name="ragione_sociale" value="{{ old('ragione_sociale', $tenant->ragione_sociale ?? $tenant->nome) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nome breve</label>
                            <input type="text" name="nome" value="{{ old('nome', $tenant->nome) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Partita IVA</label>
                            <input type="text" name="partita_iva" value="{{ old('partita_iva', $tenant->partita_iva) }}" class="form-control" maxlength="11">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Codice fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $tenant->codice_fiscale) }}" class="form-control" maxlength="16">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Regime fiscale</label>
                            <select name="regime_fiscale" class="form-select">
                                <option value="RF01" {{ ($tenant->impostazioni['regime_fiscale'] ?? '') === 'RF01' ? 'selected' : '' }}>RF01 - Ordinario</option>
                                <option value="RF19" {{ ($tenant->impostazioni['regime_fiscale'] ?? '') === 'RF19' ? 'selected' : '' }}>RF19 - Forfettario</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Codice SDI</label>
                            <input type="text" name="codice_sdi" value="{{ old('codice_sdi', $tenant->codice_sdi) }}" class="form-control" maxlength="7">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PEC</label>
                            <input type="email" name="pec" value="{{ old('pec', $tenant->pec) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $tenant->telefono) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="indirizzo">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo" value="{{ old('indirizzo', $tenant->indirizzo) }}" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Città</label>
                            <input type="text" name="citta" value="{{ old('citta', $tenant->citta) }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" value="{{ old('provincia', $tenant->provincia) }}" class="form-control" maxlength="2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap" value="{{ old('cap', $tenant->cap) }}" class="form-control" maxlength="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paese</label>
                            <input type="text" name="paese" value="{{ old('paese', $tenant->paese ?? 'Italia') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="fatturazione">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prefisso fattura</label>
                            <input type="text" name="prefisso_fattura" value="{{ old('prefisso_fattura', $tenant->impostazioni['prefisso_fattura'] ?? '') }}" class="form-control" placeholder="es. YYYY-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prossimo numero fattura</label>
                            <input type="number" name="prossimo_numero_fattura" value="{{ old('prossimo_numero_fattura', $tenant->impostazioni['prossimo_numero_fattura'] ?? 1) }}" class="form-control" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giorni scadenza default</label>
                            <input type="number" name="giorni_scadenza_fattura" value="{{ old('giorni_scadenza_fattura', $tenant->impostazioni['giorni_scadenza_fattura'] ?? 30) }}" class="form-control" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note a piè di pagina fattura</label>
                            <textarea name="note_fattura" rows="3" class="form-control">{{ old('note_fattura', $tenant->impostazioni['note_fattura'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="personalizzazione">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Logo aziendale</label>
                            @if($tenant->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$tenant->logo) }}" alt="Logo" style="max-height:80px">
                            </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Colore primario</label>
                            <input type="color" name="colore_primario" value="{{ old('colore_primario', $tenant->colore_primario ?? '#2563eb') }}" class="form-control form-control-color w-100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Colore secondario</label>
                            <input type="color" name="colore_secondario" value="{{ old('colore_secondario', $tenant->colore_secondario ?? '#1e40af') }}" class="form-control form-control-color w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-check-lg me-1"></i>Salva impostazioni
        </button>
    </div>
</form>
@endsection
