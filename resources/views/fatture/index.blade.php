@extends('layouts.app')
@section('titolo', 'Fatture')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Fatture</h5>
    @can('crea_fatture')
    <a href="{{ route('fatture.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuova Fattura
    </a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-end">
            <div class="col-sm-2">
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Tutti i tipi</option>
                    <option value="fattura" {{ request('tipo') === 'fattura' ? 'selected' : '' }}>Fattura</option>
                    <option value="nota_credito" {{ request('tipo') === 'nota_credito' ? 'selected' : '' }}>Nota Credito</option>
                    <option value="fattura_acquisto" {{ request('tipo') === 'fattura_acquisto' ? 'selected' : '' }}>Fattura Acquisto</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="stato" class="form-select form-select-sm">
                    <option value="">Tutti gli stati</option>
                    @foreach(['bozza', 'emessa', 'pagata_parzialmente', 'pagata', 'scaduta'] as $s)
                    <option value="{{ $s }}" {{ request('stato') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="anno" class="form-select form-select-sm">
                    @foreach($anni as $a)
                    <option value="{{ $a }}" {{ request('anno') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtra</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Numero</th><th>Cliente/Fornitore</th><th>Data</th><th>Scadenza</th><th>Totale</th><th>Pagato</th><th>Stato</th><th></th></tr>
            </thead>
            <tbody>
                @php $colori = ['bozza' => 'secondary', 'emessa' => 'primary', 'pagata_parzialmente' => 'warning', 'pagata' => 'success', 'scaduta' => 'danger', 'annullata' => 'secondary']; @endphp
                @forelse($fatture as $fattura)
                <tr>
                    <td><strong>{{ $fattura->numero }}</strong></td>
                    <td>{{ $fattura->cliente?->ragione_sociale ?? $fattura->fornitore?->ragione_sociale ?? '—' }}</td>
                    <td>{{ $fattura->data->format('d/m/Y') }}</td>
                    <td>
                        @if($fattura->data_scadenza)
                            <span class="{{ $fattura->data_scadenza->isPast() && !in_array($fattura->stato, ['pagata', 'annullata']) ? 'text-danger fw-semibold' : '' }}">
                                {{ $fattura->data_scadenza->format('d/m/Y') }}
                            </span>
                        @else —
                        @endif
                    </td>
                    <td class="fw-semibold">€ {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                    <td>€ {{ number_format($fattura->pagato, 2, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $colori[$fattura->stato] ?? 'secondary' }} rounded-pill">
                            {{ $fattura->stato_label }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('fatture.show', $fattura) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('fatture.pdf', $fattura) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-receipt fs-3 d-block mb-2"></i>Nessuna fattura trovata
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $fatture->links() }}</div>
</div>
@endsection
