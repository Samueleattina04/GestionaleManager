@extends('layouts.auth')
@section('titolo', 'Registrazione')
@section('sottotitolo', 'Crea il tuo account aziendale')

@section('contenuto')
<form action="{{ route('register.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Ragione Sociale Azienda</label>
        <input type="text" name="ragione_sociale" class="form-control @error('ragione_sociale') is-invalid @enderror"
               value="{{ old('ragione_sociale') }}" placeholder="La Tua Azienda S.r.l." required>
        @error('ragione_sociale')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Il tuo nome</label>
        <input type="text" name="nome_utente" class="form-control @error('nome_utente') is-invalid @enderror"
               value="{{ old('nome_utente') }}" placeholder="Mario Rossi" required>
        @error('nome_utente')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="mario@azienda.it" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Almeno 8 caratteri" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Conferma Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Ripeti la password" required>
        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input @error('accetta_termini') is-invalid @enderror" id="termini" name="accetta_termini" value="1">
        <label class="form-check-label text-muted" for="termini" style="font-size:0.875rem">
            Accetto i <a href="#">termini e condizioni</a>
        </label>
        @error('accetta_termini')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-person-plus me-2"></i>Crea Account
    </button>
</form>
<div class="text-center mt-4">
    <a href="{{ route('login') }}" style="font-size:0.875rem">Hai già un account? Accedi</a>
</div>
@endsection
