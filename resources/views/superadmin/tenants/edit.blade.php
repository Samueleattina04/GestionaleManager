@extends('layouts.superadmin')
@section('titolo', 'Modifica Azienda')

@section('contenuto')
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form action="{{ route('superadmin.tenant.update', $tenant) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Ragione Sociale *</label>
                <input type="text" name="ragione_sociale" class="form-control" value="{{ old('ragione_sociale', $tenant->ragione_sociale) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Piano</label>
                <select name="piano" class="form-select">
                    @foreach(['base', 'standard', 'premium'] as $p)
                    <option value="{{ $p }}" {{ $tenant->piano === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Scadenza Abbonamento</label>
                <input type="date" name="scadenza_abbonamento" class="form-control" value="{{ old('scadenza_abbonamento', $tenant->scadenza_abbonamento?->format('Y-m-d')) }}">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="attivo" id="attivo" value="1" {{ $tenant->attivo ? 'checked' : '' }}>
                <label class="form-check-label" for="attivo">Azienda attiva</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('superadmin.tenant.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
