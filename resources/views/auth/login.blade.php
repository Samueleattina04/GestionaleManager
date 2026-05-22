@extends('layouts.auth')
@section('titolo', 'Accedi')
@section('sottotitolo', 'Accedi al tuo account')

@section('contenuto')
<form action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="nome@azienda.it" required autofocus>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label d-flex justify-content-between">
            Password
            <a href="{{ route('password.request') }}" class="text-primary" style="font-size:0.8rem">Password dimenticata?</a>
        </label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="••••••••" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="ricordami" name="ricordami">
        <label class="form-check-label text-muted" for="ricordami" style="font-size:0.875rem">Ricordami</label>
    </div>
    <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right me-2"></i>Accedi
    </button>
</form>
<div class="text-center mt-4">
    <span class="text-muted" style="font-size:0.875rem">Non hai un account?</span>
    <a href="{{ route('register') }}" class="ms-1" style="font-size:0.875rem">Registra la tua azienda</a>
</div>
@endsection
