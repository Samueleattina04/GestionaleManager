@extends('layouts.auth')
@section('titolo', 'Nuova Password')
@section('sottotitolo', 'Imposta la tua nuova password')

@section('contenuto')
<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nuova Password</label>
        <input type="password" name="password" class="form-control" placeholder="Almeno 8 caratteri" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Conferma Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Ripeti la password" required>
        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-key me-2"></i>Reimposta Password
    </button>
</form>
@endsection
