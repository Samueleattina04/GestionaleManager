@extends('layouts.app')
@section('titolo', 'Preventivi')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Preventivi</h5>
    @can('crea_preventivi')
    <a href="{{ route('preventivi.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Preventivo
    </a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Numero</th><th>Cliente</th><th>Data</th><th>Scadenza</th><th>Totale</th><th>Stato</th><th></th></tr>
            </thead>
            <tbody>
                @php $colori = ['bozza' => 'secondary', 'inviato' => 'info', 'accettato' => 'success', 'rifiutato' => 'danger', 'scaduto' => 'warning']; @endphp
                @forelse($preventivi as $p)
                <tr>
                    <td><strong>{{ $p->numero }}</strong></td>
                    <td>{{ $p->cliente->ragione_sociale }}</td>
                    <td>{{ $p->data->format('d/m/Y') }}</td>
                    <td>{{ $p->data_scadenza?->format('d/m/Y') ?? '—' }}</td>
                    <td class="fw-semibold">€ {{ number_format($p->totale, 2, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $colori[$p->stato] ?? 'secondary' }}">{{ $p->stato_label }}</span></td>
                    <td>
                        <a href="{{ route('preventivi.show', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('preventivi.pdf', $p) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-file-text fs-3 d-block mb-2"></i>Nessun preventivo trovato
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $preventivi->links() }}</div>
</div>
@endsection
