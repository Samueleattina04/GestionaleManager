@extends('layouts.app')
@section('titolo', 'Nuovo Magazzino')

@section('contenuto')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('magazzini.index') }}" class="text-muted text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Magazzini
    </a>
    <span class="text-muted">/</span>
    <span class="fw-semibold">Nuovo Magazzino</span>
</div>

<div class="card" style="max-width:860px">
    <div class="card-header">
        <i class="bi bi-building me-2"></i>Dati magazzino
    </div>
    <div class="card-body">
        <form action="{{ route('magazzini.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label fw-medium">Nome magazzino *</label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                           value="{{ old('nome') }}" required placeholder="es. Magazzino Centrale">
                    @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-4">
                    <label class="form-label fw-medium">Responsabile</label>
                    <select name="responsabile_id" class="form-select @error('responsabile_id') is-invalid @enderror">
                        <option value="">— Nessuno —</option>
                        @foreach($utenti as $utente)
                        <option value="{{ $utente->id }}" {{ old('responsabile_id') == $utente->id ? 'selected' : '' }}>
                            {{ $utente->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('responsabile_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Descrizione</label>
                    <textarea name="descrizione" class="form-control @error('descrizione') is-invalid @enderror"
                              rows="2" placeholder="Descrizione opzionale...">{{ old('descrizione') }}</textarea>
                    @error('descrizione')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Indirizzo</label>
                    <input type="text" name="indirizzo" class="form-control @error('indirizzo') is-invalid @enderror"
                           value="{{ old('indirizzo') }}" placeholder="Via, numero civico...">
                    @error('indirizzo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-5">
                    <label class="form-label fw-medium">Città</label>
                    <input type="text" name="citta" class="form-control @error('citta') is-invalid @enderror"
                           value="{{ old('citta') }}">
                    @error('citta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-3">
                    <label class="form-label fw-medium">Provincia</label>
                    <input type="text" name="provincia" class="form-control @error('provincia') is-invalid @enderror"
                           value="{{ old('provincia') }}" maxlength="2" placeholder="es. MI">
                    @error('provincia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-4">
                    <label class="form-label fw-medium">CAP</label>
                    <input type="text" name="cap" class="form-control @error('cap') is-invalid @enderror"
                           value="{{ old('cap') }}" maxlength="5" placeholder="es. 20100">
                    @error('cap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="principale" id="principale"
                                       value="1" {{ old('principale') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="principale">
                                    Magazzino principale
                                </label>
                                <div class="form-text">Il magazzino principale viene utilizzato come deposito predefinito.</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="attivo" id="attivo"
                                       value="1" {{ old('attivo', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="attivo">
                                    Attivo
                                </label>
                                <div class="form-text">I magazzini inattivi non sono selezionabili nei movimenti.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Salva Magazzino
                </button>
                <a href="{{ route('magazzini.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
