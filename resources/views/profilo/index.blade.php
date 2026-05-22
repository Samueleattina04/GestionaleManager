@extends('layouts.app')
@section('titolo', 'Profilo Utente')

@section('contenuto')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header fw-semibold">Dati Personali</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ $utente->avatar_url }}" alt="{{ $utente->name }}" class="rounded-circle" width="80" height="80">
                    <h6 class="mt-2 mb-0 fw-semibold">{{ $utente->name }}</h6>
                    <small class="text-muted">{{ $utente->getRoleNames()->join(', ') }}</small>
                </div>
                <form action="{{ route('profilo.aggiorna') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome completo</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $utente->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $utente->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Telefono</label>
                        <input type="tel" name="telefono" class="form-control" value="{{ old('telefono', $utente->telefono) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Foto profilo</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header fw-semibold">Cambia Password</div>
            <div class="card-body">
                <form action="{{ route('profilo.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password attuale</label>
                        <input type="password" name="password_attuale" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nuova password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Conferma nuova password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Aggiorna Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
