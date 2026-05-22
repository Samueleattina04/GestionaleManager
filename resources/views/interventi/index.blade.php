@extends('layouts.app')
@section('titolo', 'Interventi')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Gestione Interventi</h5>
    @can('crea_interventi')
    <a href="{{ route('interventi.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Intervento
    </a>
    @endcan
</div>

<!-- Filtri -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="cerca" class="form-control form-control-sm" placeholder="Cerca..." value="{{ request('cerca') }}">
            </div>
            <div class="col-sm-2">
                <select name="stato" class="form-select form-select-sm">
                    <option value="">Tutti gli stati</option>
                    @foreach(['da_assegnare' => 'Da assegnare', 'assegnato' => 'Assegnato', 'in_corso' => 'In corso', 'completato' => 'Completato', 'annullato' => 'Annullato'] as $v => $l)
                    <option value="{{ $v }}" {{ request('stato') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="priorita" class="form-select form-select-sm">
                    <option value="">Tutte le priorità</option>
                    <option value="urgente" {{ request('priorita') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    <option value="normale" {{ request('priorita') === 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="bassa" {{ request('priorita') === 'bassa' ? 'selected' : '' }}>Bassa</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="tecnico" class="form-select form-select-sm">
                    <option value="">Tutti i tecnici</option>
                    @foreach($tecnici as $t)
                    <option value="{{ $t->id }}" {{ request('tecnico') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Filtra
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Numero</th><th>Cliente</th><th>Titolo</th>
                    <th>Priorità</th><th>Stato</th><th>Tecnico</th><th>Data Pianificata</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($interventi as $intervento)
                <tr>
                    <td><small class="text-muted">{{ $intervento->numero }}</small></td>
                    <td>{{ $intervento->cliente->ragione_sociale }}</td>
                    <td>
                        <a href="{{ route('interventi.show', $intervento) }}" class="text-decoration-none fw-medium">
                            {{ Str::limit($intervento->titolo, 50) }}
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-priorita-{{ $intervento->priorita }} rounded-pill">
                            {{ $intervento->priorita_label }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-stato-{{ $intervento->stato }} rounded-pill">
                            {{ $intervento->stato_label }}
                        </span>
                    </td>
                    <td>{{ $intervento->tecnico?->name ?? '<span class="text-muted">—</span>' }}</td>
                    <td>
                        <small>{{ $intervento->data_pianificata?->format('d/m/Y H:i') ?? '—' }}</small>
                    </td>
                    <td>
                        <a href="{{ route('interventi.show', $intervento) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-tools fs-3 d-block mb-2"></i>Nessun intervento trovato
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $interventi->links() }}</div>
</div>
@endsection
