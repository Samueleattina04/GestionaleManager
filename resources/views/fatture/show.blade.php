@extends('layouts.app')
@section('titolo', 'Fattura ' . $fattura->numero)

@section('contenuto')
@php
    $colori = ['bozza' => 'secondary', 'emessa' => 'primary', 'pagata_parzialmente' => 'warning', 'pagata' => 'success', 'scaduta' => 'danger', 'annullata' => 'secondary'];
@endphp

<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('fatture.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Fatture</a>
        <span class="text-muted">/</span>
        <span class="fw-semibold">{{ $fattura->numero }}</span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @can('modifica_fatture')
        <a href="{{ route('fatture.edit', $fattura) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        @endcan
        <a href="{{ route('fatture.pdf', $fattura) }}" class="btn btn-sm btn-outline-danger" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Scarica PDF
        </a>
        @if($fattura->tipo === 'fattura')
        <a href="{{ route('fatture.xml', $fattura) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
            <i class="bi bi-filetype-xml me-1"></i>Scarica XML
        </a>
        @endif
        <a href="{{ route('fatture.index') }}" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left me-1"></i>Indietro
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold fs-5">
                {{ $fattura->tipo === 'fattura' ? 'Fattura' : ($fattura->tipo === 'nota_credito' ? 'Nota di Credito' : 'Fattura Acquisto') }}
                n. {{ $fattura->numero }}
            </span>
            <span class="text-muted">del {{ $fattura->data->format('d/m/Y') }}</span>
        </div>
        <span class="badge bg-{{ $colori[$fattura->stato] ?? 'secondary' }} rounded-pill fs-6">
            {{ $fattura->stato_label }}
        </span>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                @if($fattura->cliente_id)
                    <i class="bi bi-person me-2"></i>Cliente
                @else
                    <i class="bi bi-truck me-2"></i>Fornitore
                @endif
            </div>
            <div class="card-body">
                @if($fattura->cliente)
                    <div class="fw-semibold fs-6 mb-1">
                        <a href="{{ route('clienti.show', $fattura->cliente) }}" class="text-decoration-none">{{ $fattura->cliente->ragione_sociale }}</a>
                    </div>
                    @if($fattura->cliente->partita_iva)
                    <div class="text-muted small">P.IVA: {{ $fattura->cliente->partita_iva }}</div>
                    @endif
                    @if($fattura->cliente->codice_fiscale)
                    <div class="text-muted small">C.F.: {{ $fattura->cliente->codice_fiscale }}</div>
                    @endif
                    @if($fattura->cliente->indirizzo)
                    <div class="text-muted small">{{ $fattura->cliente->indirizzo_completo }}</div>
                    @endif
                    @if($fattura->cliente->email)
                    <div class="small mt-1"><a href="mailto:{{ $fattura->cliente->email }}">{{ $fattura->cliente->email }}</a></div>
                    @endif
                    @if($fattura->cliente->telefono)
                    <div class="small"><a href="tel:{{ $fattura->cliente->telefono }}">{{ $fattura->cliente->telefono }}</a></div>
                    @endif
                    @if($fattura->cliente->codice_sdi)
                    <div class="text-muted small mt-1">SDI: {{ $fattura->cliente->codice_sdi }}</div>
                    @endif
                    @if($fattura->cliente->pec)
                    <div class="text-muted small">PEC: {{ $fattura->cliente->pec }}</div>
                    @endif
                @elseif($fattura->fornitore)
                    <div class="fw-semibold fs-6 mb-1">{{ $fattura->fornitore->ragione_sociale }}</div>
                    @if($fattura->fornitore->partita_iva)
                    <div class="text-muted small">P.IVA: {{ $fattura->fornitore->partita_iva }}</div>
                    @endif
                    @if($fattura->fornitore->indirizzo)
                    <div class="text-muted small">{{ $fattura->fornitore->indirizzo }}</div>
                    @endif
                    @if($fattura->fornitore->email)
                    <div class="small mt-1"><a href="mailto:{{ $fattura->fornitore->email }}">{{ $fattura->fornitore->email }}</a></div>
                    @endif
                @else
                    <span class="text-muted">—</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-2"></i>Dettagli Fattura</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Data Emissione</small>
                        <span>{{ ($fattura->data_emissione ?? $fattura->data)->format('d/m/Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Data Scadenza</small>
                        @if($fattura->data_scadenza)
                            <span class="{{ $fattura->data_scadenza->isPast() && !in_array($fattura->stato, ['pagata', 'annullata']) ? 'text-danger fw-semibold' : '' }}">
                                {{ $fattura->data_scadenza->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Metodo di Pagamento</small>
                        <span>{{ $fattura->metodo_pagamento ? ucfirst(str_replace('_', ' ', $fattura->metodo_pagamento)) : '—' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Tipo Documento</small>
                        <span>{{ ucfirst(str_replace('_', ' ', $fattura->tipo)) }}</span>
                    </div>
                    @if($fattura->note)
                    <div class="col-12">
                        <small class="text-muted fw-semibold text-uppercase d-block">Note</small>
                        <span class="small">{{ $fattura->note }}</span>
                    </div>
                    @endif
                    @if($fattura->condizioni)
                    <div class="col-12">
                        <small class="text-muted fw-semibold text-uppercase d-block">Condizioni</small>
                        <span class="small">{{ $fattura->condizioni }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold"><i class="bi bi-list-ul me-2"></i>Righe Fattura</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Descrizione</th>
                    <th class="text-end">Q.tà</th>
                    <th>U.M.</th>
                    <th class="text-end">Prezzo Unit.</th>
                    <th class="text-end">Sconto %</th>
                    <th class="text-end">IVA %</th>
                    <th class="text-end">Totale Riga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fattura->righe as $riga)
                <tr>
                    <td>{{ $riga->descrizione }}</td>
                    <td class="text-end">{{ number_format($riga->quantita, 2, ',', '.') }}</td>
                    <td>{{ $riga->unita_misura ?? '—' }}</td>
                    <td class="text-end">€ {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}</td>
                    <td class="text-end">
                        @php $sconto = $riga->sconto_percentuale ?? $riga->sconto ?? 0; @endphp
                        {{ $sconto > 0 ? number_format($sconto, 1, ',', '.') . '%' : '—' }}
                    </td>
                    <td class="text-end">{{ number_format($riga->aliquota_iva ?? $riga->iva ?? 0, 0) }}%</td>
                    <td class="text-end fw-semibold">€ {{ number_format($riga->totale, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="6" class="text-end fw-medium">Imponibile</td>
                    <td class="text-end fw-medium">€ {{ number_format($fattura->totale_imponibile, 2, ',', '.') }}</td>
                </tr>
                <tr class="table-light">
                    <td colspan="6" class="text-end fw-medium">Totale IVA</td>
                    <td class="text-end fw-medium">€ {{ number_format($fattura->totale_iva, 2, ',', '.') }}</td>
                </tr>
                <tr class="table-primary">
                    <td colspan="6" class="text-end fw-bold fs-6">Totale Fattura</td>
                    <td class="text-end fw-bold fs-6">€ {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-cash-stack me-2"></i>Pagamenti</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                Saldo residuo:
                <strong class="{{ ($fattura->saldo ?? ($fattura->totale - ($fattura->pagato ?? 0))) > 0 ? 'text-danger' : 'text-success' }}">
                    € {{ number_format($fattura->saldo ?? ($fattura->totale - ($fattura->pagato ?? 0)), 2, ',', '.') }}
                </strong>
            </span>
            @can('registra_pagamenti')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPagamento">
                <i class="bi bi-plus-lg me-1"></i>Registra Pagamento
            </button>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th class="text-end">Importo</th>
                    <th>Metodo</th>
                    <th>Riferimento</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fattura->pagamenti as $pagamento)
                <tr>
                    <td>{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
                    <td class="text-end fw-semibold text-success">€ {{ number_format($pagamento->importo, 2, ',', '.') }}</td>
                    <td>{{ $pagamento->metodo ? ucfirst(str_replace('_', ' ', $pagamento->metodo)) : '—' }}</td>
                    <td>{{ $pagamento->riferimento ?? '—' }}</td>
                    <td>{{ $pagamento->note ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-3 text-muted">
                        <i class="bi bi-cash fs-4 d-block mb-1"></i>Nessun pagamento registrato
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalPagamento" tabindex="-1" aria-labelledby="modalPagamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fatture.registra-pagamento', $fattura) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPagamentoLabel"><i class="bi bi-cash me-2"></i>Registra Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-medium">Importo *</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" name="importo" class="form-control" step="0.01" min="0.01"
                                    value="{{ number_format($fattura->saldo ?? ($fattura->totale - ($fattura->pagato ?? 0)), 2, '.', '') }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-medium">Data Pagamento *</label>
                            <input type="date" name="data_pagamento" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Metodo *</label>
                            <select name="metodo" class="form-select" required>
                                <option value="bonifico">Bonifico</option>
                                <option value="contanti">Contanti</option>
                                <option value="assegno">Assegno</option>
                                <option value="carta">Carta</option>
                                <option value="rid">RID / SDD</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Riferimento</label>
                            <input type="text" name="riferimento" class="form-control" placeholder="N. bonifico, assegno...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Note</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Registra</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
