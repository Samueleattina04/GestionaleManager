@extends('layouts.app')
@section('titolo', 'Preferenze notifiche')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Preferenze notifiche</h1>
    <a href="{{ route('notifiche.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Notifiche
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('notifiche.preferenze.aggiorna') }}">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header">
            <div class="row fw-semibold">
                <div class="col-6">Tipo notifica</div>
                <div class="col-2 text-center">Email</div>
                <div class="col-2 text-center">Push</div>
                <div class="col-2 text-center">In-app</div>
            </div>
        </div>
        <ul class="list-group list-group-flush">
            @php
            $tipi = [
                'nuovo_intervento' => 'Nuovo intervento assegnato',
                'modifica_intervento' => 'Modifica intervento',
                'scadenza_contratto' => 'Scadenza contratto',
                'sotto_scorta' => 'Articolo sotto scorta minima',
                'nuova_fattura' => 'Nuova fattura ricevuta',
                'pagamento_ricevuto' => 'Pagamento ricevuto',
                'nuovo_preventivo' => 'Nuovo preventivo',
            ];
            @endphp
            @foreach($tipi as $tipo => $label)
            @php
            $pref = $preferenze->firstWhere('tipo', $tipo);
            @endphp
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-6">
                        <strong>{{ $label }}</strong>
                    </div>
                    <div class="col-2 text-center">
                        <div class="form-check d-inline-block">
                            <input type="hidden" name="preferenze[{{ $tipo }}][email]" value="0">
                            <input type="checkbox" name="preferenze[{{ $tipo }}][email]" value="1"
                                class="form-check-input"
                                {{ ($pref->email ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="form-check d-inline-block">
                            <input type="hidden" name="preferenze[{{ $tipo }}][push]" value="0">
                            <input type="checkbox" name="preferenze[{{ $tipo }}][push]" value="1"
                                class="form-check-input"
                                {{ ($pref->push ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="form-check d-inline-block">
                            <input type="hidden" name="preferenze[{{ $tipo }}][in_app]" value="0">
                            <input type="checkbox" name="preferenze[{{ $tipo }}][in_app]" value="1"
                                class="form-check-input"
                                {{ ($pref->in_app ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salva preferenze
            </button>
        </div>
    </div>
</form>
@endsection
