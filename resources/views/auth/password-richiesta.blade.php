@extends('layouts.auth')
@section('titolo', 'Reset Password')
@section('sottotitolo', 'Inserisci la tua email per reimpostare la password')

@section('contenuto')
<form action="{{ route('password.email') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="nome@azienda.it" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-envelope me-2"></i>Invia link di reset
    </button>
</form>
<div class="text-center mt-3">
    <a href="{{ route('login') }}" style="font-size:0.875rem"><i class="bi bi-arrow-left me-1"></i>Torna al login</a>
</div>
@endsection
