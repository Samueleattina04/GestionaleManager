@extends('layouts.app')
@section('titolo', 'Articoli')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Articoli</h5>
    @can('modifica_articoli')
    <a href="{{ route('articoli.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Articolo
    </a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="cerca" class="form-control form-control-sm" placeholder="Cerca codice o descrizione..." value="{{ request('cerca') }}">
            </div>
            <div class="col-sm-2">
                <select name="categoria" class="form-select form-select-sm">
                    <option value="">Tutte le categorie</option>
                    @foreach($categorie as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <div class="form-check mt-1">
                    <input type="checkbox" class="form-check-input" name="sotto_scorta" id="ss" value="1" {{ request('sotto_scorta') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="ss">Solo sotto scorta</label>
                </div>
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
                <tr><th>Codice</th><th>Descrizione</th><th>Categoria</th><th>U.M.</th><th>Prezzo Vendita</th><th>Giacenza Totale</th><th>Scorta Min.</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($articoli as $articolo)
                <tr class="{{ $articolo->isSottoScortaMinima() ? 'table-warning' : '' }}">
                    <td><small class="text-muted">{{ $articolo->codice ?? '—' }}</small></td>
                    <td>
                        <a href="{{ route('articoli.show', $articolo) }}" class="text-decoration-none fw-medium">
                            {{ $articolo->descrizione }}
                        </a>
                    </td>
                    <td>{{ $articolo->categoria?->nome ?? '—' }}</td>
                    <td>{{ $articolo->unita_misura }}</td>
                    <td>€ {{ number_format($articolo->prezzo_vendita, 2, ',', '.') }}</td>
                    <td>
                        <span class="{{ $articolo->isSottoScortaMinima() ? 'text-danger fw-bold' : '' }}">
                            {{ number_format($articolo->giacenzaTotale(), 2, ',', '.') }}
                        </span>
                        @if($articolo->isSottoScortaMinima())
                            <i class="bi bi-exclamation-triangle text-danger ms-1"></i>
                        @endif
                    </td>
                    <td>{{ number_format($articolo->scorta_minima, 2, ',', '.') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('articoli.show', $articolo) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @can('modifica_articoli')
                            <a href="{{ route('articoli.edit', $articolo) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-box-seam fs-3 d-block mb-2"></i>Nessun articolo trovato
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $articoli->links() }}</div>
</div>
@endsection
