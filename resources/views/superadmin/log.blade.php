@extends('layouts.superadmin')
@section('titolo', 'Log Attività')

@section('contenuto')
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Data</th><th>Utente</th><th>Azienda</th><th>Azione</th><th>Modello</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>{{ $log->utente?->name ?? '—' }}</td>
                    <td>{{ $log->tenant?->ragione_sociale ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ $log->azione }}</span></td>
                    <td>{{ $log->modello }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Nessun log disponibile</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $logs->links() }}</div>
</div>
@endsection
