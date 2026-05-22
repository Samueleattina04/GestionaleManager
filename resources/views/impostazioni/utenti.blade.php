@extends('layouts.app')
@section('titolo', 'Gestione utenti')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Gestione utenti</h1>
    <div>
        <a href="{{ route('impostazioni') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i>Impostazioni
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovoUtente">
            <i class="bi bi-person-plus me-1"></i>Nuovo utente
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Utente</th>
                    <th>Email</th>
                    <th>Ruolo</th>
                    <th class="text-center">Stato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($utenti as $utente)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($utente->avatar)
                                <img src="{{ asset('storage/'.$utente->avatar) }}" class="rounded-circle me-2" width="36" height="36" style="object-fit:cover">
                            @else
                                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px">
                                    {{ strtoupper(substr($utente->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $utente->name }}</div>
                                <small class="text-muted">{{ $utente->telefono }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $utente->email }}</td>
                    <td>
                        @foreach($utente->getRoleNames() as $ruolo)
                        <span class="badge bg-{{ $ruolo === 'titolare' ? 'primary' : ($ruolo === 'amministratore' ? 'info' : ($ruolo === 'tecnico' ? 'success' : ($ruolo === 'commerciale' ? 'warning' : 'secondary'))) }}">
                            {{ ucfirst($ruolo) }}
                        </span>
                        @endforeach
                    </td>
                    <td class="text-center">
                        @if($utente->attivo)
                            <span class="badge bg-success">Attivo</span>
                        @else
                            <span class="badge bg-secondary">Inattivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($utente->id !== auth()->id())
                        <button type="button" class="btn btn-sm btn-outline-primary me-1"
                            data-bs-toggle="modal" data-bs-target="#modalEditUtente"
                            data-id="{{ $utente->id }}" data-name="{{ $utente->name }}"
                            data-ruolo="{{ $utente->getRoleNames()->first() }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('impostazioni.utenti.elimina', $utente) }}" class="d-inline"
                              onsubmit="return confirm('Eliminare l\'utente {{ $utente->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nessun utente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($utenti->hasPages())
    <div class="card-footer">{{ $utenti->links() }}</div>
    @endif
</div>

<!-- Modal nuovo utente -->
<div class="modal fade" id="modalNuovoUtente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('impostazioni.utenti.crea') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuovo utente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ruolo <span class="text-danger">*</span></label>
                            <select name="ruolo" class="form-select" required>
                                @foreach($ruoli as $ruolo)
                                <option value="{{ $ruolo->name }}">{{ ucfirst($ruolo->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Crea utente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal modifica ruolo -->
<div class="modal fade" id="modalEditUtente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditUtente">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Modifica utente: <span id="editNomeUtente"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Ruolo</label>
                    <select name="ruolo" class="form-select" id="editRuoloSelect">
                        @foreach($ruoli as $ruolo)
                        <option value="{{ $ruolo->name }}">{{ ucfirst($ruolo->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('modalEditUtente').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('editNomeUtente').textContent = btn.dataset.name;
    document.getElementById('formEditUtente').action = '/impostazioni/utenti/' + btn.dataset.id;
    document.getElementById('editRuoloSelect').value = btn.dataset.ruolo;
});
</script>
@endsection
