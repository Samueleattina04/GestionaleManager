@extends('layouts.app')
@section('titolo', 'Fornitori')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Fornitori</h1>
    <a href="{{ route('fornitori.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo fornitore
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="ricerca" value="{{ request('ricerca') }}" class="form-control" placeholder="Cerca per ragione sociale, P.IVA, referente...">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="solo_attivi" id="solo_attivi" class="form-check-input" value="1" {{ request('solo_attivi') ? 'checked' : '' }}>
                    <label for="solo_attivi" class="form-check-label">Solo attivi</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Cerca</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Codice</th>
                    <th>Ragione sociale</th>
                    <th>P. IVA</th>
                    <th>Città</th>
                    <th>Referente</th>
                    <th>Telefono</th>
                    <th>Email</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($fornitori as $fornitore)
                <tr>
                    <td><code>{{ $fornitore->codice }}</code></td>
                    <td><strong>{{ $fornitore->ragione_sociale }}</strong></td>
                    <td>{{ $fornitore->partita_iva }}</td>
                    <td>{{ $fornitore->citta }}</td>
                    <td>{{ $fornitore->referente }}</td>
                    <td>{{ $fornitore->telefono }}</td>
                    <td>{{ $fornitore->email }}</td>
                    <td>
                        @if($fornitore->attivo)
                            <span class="badge bg-success">Attivo</span>
                        @else
                            <span class="badge bg-secondary">Inattivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('fornitori.show', $fornitore) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('fornitori.edit', $fornitore) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('fornitori.destroy', $fornitore) }}" class="d-inline"
                              onsubmit="return confirm('Eliminare il fornitore {{ $fornitore->ragione_sociale }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Nessun fornitore trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fornitori->hasPages())
    <div class="card-footer">{{ $fornitori->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
