@extends('layouts.superadmin')
@section('titolo', 'Nuova Azienda')

@section('contenuto')
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form action="{{ route('superadmin.tenant.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Ragione Sociale *</label>
                <input type="text" name="ragione_sociale" class="form-control" value="{{ old('ragione_sociale') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Slug (URL) *</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required placeholder="nome-azienda">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Piano</label>
                <select name="piano" class="form-select">
                    <option value="base">Base</option>
                    <option value="standard">Standard</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Crea Azienda</button>
                <a href="{{ route('superadmin.tenant.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
