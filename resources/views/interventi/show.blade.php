@extends('layouts.app')
@section('titolo', 'Intervento ' . $intervento->numero)

@section('contenuto')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('interventi.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Interventi</a>
        <span class="text-muted">/</span>
        <span class="fw-semibold">{{ $intervento->numero }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('interventi.pdf', $intervento) }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-filetype-pdf me-1"></i>PDF
        </a>
        @can('modifica_interventi')
        <a href="{{ route('interventi.edit', $intervento) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        @if(!$intervento->fatturato && $intervento->stato === 'completato')
        <form action="{{ route('interventi.converti-fattura', $intervento) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Convertire in fattura?')">
                <i class="bi bi-receipt me-1"></i>Converti in Fattura
            </button>
        </form>
        @endif
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Info principali -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ $intervento->titolo }}</span>
                <div class="d-flex gap-2">
                    <span class="badge badge-priorita-{{ $intervento->priorita }}">{{ $intervento->priorita_label }}</span>
                    <span class="badge badge-stato-{{ $intervento->stato }}">{{ $intervento->stato_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Cliente</small>
                        <a href="{{ route('clienti.show', $intervento->cliente) }}" class="fw-semibold text-decoration-none">
                            {{ $intervento->cliente->ragione_sociale }}
                        </a>
                        @if($intervento->cliente->telefono)
                        <br><small><a href="tel:{{ $intervento->cliente->telefono }}">{{ $intervento->cliente->telefono }}</a></small>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Tecnico</small>
                        <span>{{ $intervento->tecnico?->name ?? '— Non assegnato' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Data Pianificata</small>
                        <span>{{ $intervento->data_pianificata?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Tempo Lavorato</small>
                        <span>{{ intdiv($intervento->minuti_lavorati, 60) }}h {{ $intervento->minuti_lavorati % 60 }}min</span>
                    </div>
                    @if($intervento->descrizione)
                    <div class="col-12">
                        <small class="text-muted fw-semibold text-uppercase d-block">Descrizione</small>
                        <p class="mb-0">{{ $intervento->descrizione }}</p>
                    </div>
                    @endif
                    @if($intervento->indirizzo_intervento)
                    <div class="col-12">
                        <small class="text-muted fw-semibold text-uppercase d-block">Indirizzo Intervento</small>
                        <p class="mb-0">{{ $intervento->indirizzo_intervento }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Checklist -->
        @if($intervento->checklist->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Checklist</span>
                <span class="badge bg-secondary">{{ $intervento->checklist->where('completata', true)->count() }}/{{ $intervento->checklist->count() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @foreach($intervento->checklist as $item)
                <div class="list-group-item d-flex align-items-center gap-3">
                    <input type="checkbox" class="form-check-input flex-shrink-0"
                           {{ $item->completata ? 'checked' : '' }}
                           onchange="fetch('{{ route('interventi.checklist.aggiorna', [$intervento, $item]) }}', {method:'PATCH', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({completata:this.checked})})">
                    <span class="{{ $item->completata ? 'text-decoration-line-through text-muted' : '' }}">{{ $item->voce }}</span>
                    @if($item->completata && $item->completata_da)
                    <small class="text-muted ms-auto">{{ $item->completata_il?->format('d/m H:i') }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Foto -->
        @if($intervento->foto->count() > 0)
        <div class="card mb-3">
            <div class="card-header fw-semibold">Foto ({{ $intervento->foto->count() }})</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($intervento->foto as $foto)
                    <div class="col-6 col-sm-4 col-md-3">
                        <a href="{{ $foto->url }}" target="_blank">
                            <img src="{{ $foto->url }}" class="img-fluid rounded" style="aspect-ratio:1; object-fit:cover;">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Articoli usati -->
        @if($intervento->articoli->count() > 0)
        <div class="card mb-3">
            <div class="card-header fw-semibold">Ricambi Utilizzati</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Articolo</th><th>Q.tà</th><th>Prezzo</th><th>Totale</th></tr>
                    </thead>
                    <tbody>
                        @foreach($intervento->articoli as $ia)
                        <tr>
                            <td>{{ $ia->articolo->descrizione }}</td>
                            <td>{{ number_format($ia->quantita, 2, ',', '.') }} {{ $ia->articolo->unita_misura }}</td>
                            <td>€ {{ number_format($ia->prezzo_unitario, 2, ',', '.') }}</td>
                            <td>€ {{ number_format($ia->quantita * $ia->prezzo_unitario, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($intervento->note_cliente)
        <div class="card mb-3">
            <div class="card-header fw-semibold">Note per il Cliente</div>
            <div class="card-body">{{ $intervento->note_cliente }}</div>
        </div>
        @endif

        @if($intervento->firma_cliente)
        <div class="card mb-3">
            <div class="card-header fw-semibold"><i class="bi bi-check-circle text-success me-2"></i>Firma Cliente Acquisita</div>
            <div class="card-body">
                <img src="{{ asset('storage/' . $intervento->firma_cliente) }}" alt="Firma cliente" class="border rounded" style="max-height:80px;">
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Azioni -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">Azioni Rapide</div>
            <div class="card-body d-grid gap-2">
                <!-- Cambia stato -->
                <form action="{{ route('interventi.cambia-stato', $intervento) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <select name="stato" class="form-select form-select-sm">
                        @foreach(['da_assegnare' => 'Da assegnare', 'assegnato' => 'Assegnato', 'in_corso' => 'In corso', 'completato' => 'Completato', 'annullato' => 'Annullato'] as $v => $l)
                        <option value="{{ $v }}" {{ $intervento->stato === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">OK</button>
                </form>

                <!-- Assegna tecnico -->
                @can('assegna_interventi')
                <form action="{{ route('interventi.assegna', $intervento) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <select name="tecnico_id" class="form-select form-select-sm">
                        <option value="">— Assegna tecnico —</option>
                        @foreach($tecnici as $t)
                        <option value="{{ $t->id }}" {{ $intervento->tecnico_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">OK</button>
                </form>
                @endcan

                <!-- Timer -->
                <div class="d-flex gap-2">
                    <form action="{{ route('interventi.timer.start', $intervento) }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-play-fill me-1"></i>Start Timer</button>
                    </form>
                    <form action="{{ route('interventi.timer.stop', $intervento) }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning w-100"><i class="bi bi-stop-fill me-1"></i>Stop</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aggiungi foto -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">Carica Foto</div>
            <div class="card-body">
                <form action="{{ route('interventi.foto', $intervento) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="foto[]" class="form-control form-control-sm mb-2" accept="image/*" multiple capture="environment">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-upload me-1"></i>Carica</button>
                </form>
            </div>
        </div>

        <!-- Aggiungi voce checklist -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">Aggiungi a Checklist</div>
            <div class="card-body">
                <form action="{{ route('interventi.checklist', $intervento) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="voce" class="form-control form-control-sm" placeholder="Nuova attività..." required>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></button>
                </form>
            </div>
        </div>

        <!-- Note interne -->
        @if($intervento->note_interne)
        <div class="card mb-3">
            <div class="card-header fw-semibold">Note Interne</div>
            <div class="card-body">
                <p class="mb-0 small">{{ $intervento->note_interne }}</p>
            </div>
        </div>
        @endif

        <!-- Info temporali -->
        <div class="card">
            <div class="card-header fw-semibold">Cronologia</div>
            <div class="list-group list-group-flush">
                <div class="list-group-item px-3 py-2">
                    <small class="text-muted d-block">Creato il</small>
                    <small>{{ $intervento->created_at->format('d/m/Y H:i') }}</small>
                </div>
                @if($intervento->data_inizio_effettivo)
                <div class="list-group-item px-3 py-2">
                    <small class="text-muted d-block">Inizio effettivo</small>
                    <small>{{ $intervento->data_inizio_effettivo->format('d/m/Y H:i') }}</small>
                </div>
                @endif
                @if($intervento->data_fine_effettivo)
                <div class="list-group-item px-3 py-2">
                    <small class="text-muted d-block">Fine effettiva</small>
                    <small>{{ $intervento->data_fine_effettivo->format('d/m/Y H:i') }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
