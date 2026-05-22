@extends('layouts.app')
@section('titolo', 'Inventario - ' . $magazzino->nome)
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Inventario: {{ $magazzino->nome }}</h1>
    <div>
        <button onclick="window.print()" class="btn btn-outline-secondary me-2">
            <i class="bi bi-printer me-1"></i>Stampa
        </button>
        <a href="{{ route('magazzini.show', $magazzino) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="categoria_id" class="form-select">
                    <option value="">Tutte le categorie</option>
                    @foreach($categorie ?? [] as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filtra</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Codice</th>
                    <th>Articolo</th>
                    <th>Categoria</th>
                    <th class="text-end">Quantità</th>
                    <th>U.M.</th>
                    <th class="text-end">Prezzo unit.</th>
                    <th class="text-end">Valore totale</th>
                </tr>
            </thead>
            <tbody>
                @php $totaleValore = 0; @endphp
                @forelse($articoli as $item)
                @php
                    $valore = ($item->giacenza?->quantita ?? 0) * ($item->prezzo_acquisto ?? 0);
                    $totaleValore += $valore;
                @endphp
                <tr>
                    <td><code>{{ $item->codice }}</code></td>
                    <td>{{ $item->nome }}</td>
                    <td>{{ $item->categoria->nome ?? '-' }}</td>
                    <td class="text-end">{{ number_format($item->giacenza?->quantita ?? 0, 2) }}</td>
                    <td>{{ $item->unita_misura }}</td>
                    <td class="text-end">€ {{ number_format($item->prezzo_acquisto ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end">€ {{ number_format($valore, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nessun articolo.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="table-dark fw-bold">
                <tr>
                    <td colspan="5">Totale valore magazzino</td>
                    <td></td>
                    <td class="text-end">€ {{ number_format($totaleValore, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @if(isset($articoli) && method_exists($articoli, 'hasPages') && $articoli->hasPages())
    <div class="card-footer">{{ $articoli->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
