@extends('layouts.app')
@section('titolo', 'Contratti')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Contratti</h5>
    @can('crea_contratti')
    <a href="{{ route('contratti.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Contratto
    </a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Numero</th><th>Cliente</th><th>Tipo</th><th>Scadenza</th><th>Importo</th><th>Stato</th><th></th></tr>
            </thead>
            <tbody>
                @php $colori = ['attivo' => 'success', 'scaduto' => 'danger', 'sospeso' => 'warning', 'annullato' => 'secondary']; @endphp
                @forelse($contratti as $c)
                <tr>
                    <td><strong>{{ $c->numero }}</strong></td>
                    <td>{{ $c->cliente->ragione_sociale }}</td>
                    <td><span class="badge bg-light text-dark">{{ ucfirst($c->tipo) }}</span></td>
                    <td>
                        <span class="{{ $c->isInScadenza(30) ? 'text-warning fw-semibold' : '' }}">
                            {{ $c->data_fine->format('d/m/Y') }}
                        </span>
                        @if($c->isInScadenza(30))
                            <i class="bi bi-exclamation-triangle text-warning ms-1"></i>
                        @endif
                    </td>
                    <td>€ {{ number_format($c->importo, 2, ',', '.') }}/{{ $c->frequenza_fatturazione }}</td>
                    <td><span class="badge bg-{{ $colori[$c->stato] ?? 'secondary' }}">{{ ucfirst($c->stato) }}</span></td>
                    <td>
                        <a href="{{ route('contratti.show', $c) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-file-earmark-check fs-3 d-block mb-2"></i>Nessun contratto trovato
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $contratti->links() }}</div>
</div>
@endsection
