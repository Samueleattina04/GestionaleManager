@extends('layouts.superadmin')
@section('titolo', 'Statistiche Piattaforma')

@section('contenuto')
<div class="card">
    <div class="card-header">Statistiche per Azienda</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Azienda</th><th>Piano</th><th>Utenti</th><th>Stato</th></tr>
            </thead>
            <tbody>
                @foreach($tenant as $t)
                <tr>
                    <td>{{ $t->ragione_sociale }}</td>
                    <td>{{ ucfirst($t->piano) }}</td>
                    <td>{{ $t->utenti_count }}</td>
                    <td>{{ $t->attivo ? 'Attiva' : 'Disattiva' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
