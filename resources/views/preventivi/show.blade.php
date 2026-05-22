@extends('layouts.app')
@section('titolo', 'Preventivo ' . $preventivo->numero)

@section('contenuto')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('preventivi.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Preventivi</a>
        <span class="text-muted">/</span>
        <span class="fw-semibold">{{ $preventivo->numero }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('preventivi.pdf', $preventivo) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
        @if($preventivo->stato === 'bozza')
        <form action="{{ route('preventivi.invia', $preventivo) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-info"><i class="bi bi-send me-1"></i>Segna Inviato</button>
        </form>
        @endif
        @if(in_array($preventivo->stato, ['bozza', 'inviato']))
        <form action="{{ route('preventivi.converti-fattura', $preventivo) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Convertire in fattura?')">
                <i class="bi bi-receipt me-1"></i>Converti in Fattura
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Preventivo n. {{ $preventivo->numero }} del {{ $preventivo->data->format('d/m/Y') }}</span>
                @php $colori = ['bozza' => 'secondary', 'inviato' => 'info', 'accettato' => 'success', 'rifiutato' => 'danger', 'scaduto' => 'warning']; @endphp
                <span class="badge bg-{{ $colori[$preventivo->stato] ?? 'secondary' }}">{{ $preventivo->stato_label }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Cliente</small>
                        <strong>{{ $preventivo->cliente->ragione_sociale }}</strong>
                        <div class="text-muted small">{{ $preventivo->cliente->indirizzo_completo }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted fw-semibold text-uppercase d-block">Validità</small>
                        <div>Emesso: {{ $preventivo->data->format('d/m/Y') }}</div>
                        @if($preventivo->data_scadenza)
                        <div>Valido fino al: <strong>{{ $preventivo->data_scadenza->format('d/m/Y') }}</strong></div>
                        @endif
                    </div>
                </div>
                @if($preventivo->oggetto)
                <div class="bg-light p-3 rounded mb-3">
                    <strong>Oggetto:</strong> {{ $preventivo->oggetto }}
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr><th>Descrizione</th><th class="text-end">Q.tà</th><th>U.M.</th><th class="text-end">Prezzo</th><th class="text-end">IVA</th><th class="text-end">Totale</th></tr>
                        </thead>
                        <tbody>
                            @foreach($preventivo->righe as $riga)
                            <tr>
                                <td>{{ $riga->descrizione }}</td>
                                <td class="text-end">{{ number_format($riga->quantita, 2, ',', '.') }}</td>
                                <td>{{ $riga->unita_misura }}</td>
                                <td class="text-end">€ {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($riga->iva, 0) }}%</td>
                                <td class="text-end fw-medium">€ {{ number_format($riga->totale, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light"><td colspan="5" class="text-end fw-medium">Imponibile:</td><td class="text-end fw-medium">€ {{ number_format($preventivo->totale_imponibile, 2, ',', '.') }}</td></tr>
                            <tr class="table-light"><td colspan="5" class="text-end fw-medium">IVA:</td><td class="text-end fw-medium">€ {{ number_format($preventivo->totale_iva, 2, ',', '.') }}</td></tr>
                            <tr class="table-primary"><td colspan="5" class="text-end fw-bold">TOTALE:</td><td class="text-end fw-bold">€ {{ number_format($preventivo->totale, 2, ',', '.') }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-semibold">Aggiorna Stato</div>
            <div class="card-body">
                <form action="{{ route('preventivi.update', $preventivo) }}" method="POST">
                    @csrf @method('PUT')
                    <select name="stato" class="form-select mb-2">
                        @foreach(['bozza' => 'Bozza', 'inviato' => 'Inviato', 'accettato' => 'Accettato', 'rifiutato' => 'Rifiutato', 'scaduto' => 'Scaduto'] as $v => $l)
                        <option value="{{ $v }}" {{ $preventivo->stato === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Aggiorna</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
