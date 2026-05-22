@extends('layouts.app')
@section('titolo', 'Nuovo articolo')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nuovo articolo</h1>
    <a href="{{ route('articoli.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<form method="POST" action="{{ route('articoli.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Dati articolo</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Codice <span class="text-danger">*</span></label>
                            <input type="text" name="codice" value="{{ old('codice') }}" class="form-control @error('codice') is-invalid @enderror" required>
                            @error('codice')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" value="{{ old('nome') }}" class="form-control @error('nome') is-invalid @enderror" required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrizione</label>
                            <textarea name="descrizione" rows="3" class="form-control">{{ old('descrizione') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria</label>
                            <select name="categoria_id" class="form-select">
                                <option value="">Nessuna categoria</option>
                                @foreach($categorie as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unità di misura <span class="text-danger">*</span></label>
                            <select name="unita_misura" class="form-select @error('unita_misura') is-invalid @enderror" required>
                                @foreach(['pz' => 'Pezzi', 'm' => 'Metri', 'm2' => 'Metri quadri', 'm3' => 'Metri cubi', 'kg' => 'Kilogrammi', 'lt' => 'Litri', 'h' => 'Ore', 'gg' => 'Giorni'] as $val => $label)
                                <option value="{{ $val }}" {{ old('unita_misura') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Prezzi e IVA</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prezzo acquisto (€)</label>
                            <input type="number" name="prezzo_acquisto" value="{{ old('prezzo_acquisto', '0.00') }}" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prezzo vendita (€)</label>
                            <input type="number" name="prezzo_vendita" value="{{ old('prezzo_vendita', '0.00') }}" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aliquota IVA (%)</label>
                            <select name="aliquota_iva" class="form-select">
                                @foreach(['0', '4', '5', '10', '22'] as $iva)
                                <option value="{{ $iva }}" {{ old('aliquota_iva', '22') === $iva ? 'selected' : '' }}>{{ $iva }}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scorta minima</label>
                            <input type="number" name="scorta_minima" value="{{ old('scorta_minima', '0') }}" step="0.01" min="0" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($magazzini) && $magazzini->count() > 0)
            <div class="card">
                <div class="card-header"><strong>Giacenza iniziale</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($magazzini as $mag)
                        <div class="col-md-6">
                            <label class="form-label">{{ $mag->nome }}</label>
                            <input type="number" name="giacenze[{{ $mag->id }}]" value="{{ old('giacenze.'.$mag->id, '0') }}" step="0.01" min="0" class="form-control" placeholder="0">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>Impostazioni</strong></div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="attivo" value="0">
                        <input type="checkbox" name="attivo" id="attivo" class="form-check-input" value="1" {{ old('attivo', '1') ? 'checked' : '' }}>
                        <label for="attivo" class="form-check-label">Articolo attivo</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Salva articolo
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
