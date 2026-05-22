<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
    h1 { font-size: 18px; color: #1e3a8a; }
    .separatore { border-top: 2px solid #1e3a8a; margin: 10px 0; }
    .grid-2 { display: flex; gap: 20px; margin-bottom: 15px; }
    .col { flex: 1; }
    .campo { margin-bottom: 8px; }
    .campo label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #6b7280; display: block; }
    .campo p { margin: 2px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table thead { background: #1e3a8a; color: white; }
    table thead th { padding: 6px 8px; font-size: 10px; text-align: left; }
    table tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .checklist { margin-top: 10px; }
    .checklist-item { display: flex; align-items: center; padding: 4px 0; border-bottom: 1px solid #f3f4f6; }
    .check { width: 14px; height: 14px; border: 1px solid #6b7280; border-radius: 2px; margin-right: 8px; display: inline-block; text-align: center; line-height: 14px; font-size: 10px; }
    .check.done { background: #1e3a8a; color: white; border-color: #1e3a8a; }
    .firma-section { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    .firma-box { display: flex; gap: 40px; }
    .firma-col { flex: 1; }
    .firma-line { border-top: 1px solid #1a1a1a; margin-top: 50px; padding-top: 5px; font-size: 9px; text-align: center; }
</style>
</head>
<body>
<div style="display:flex; justify-content:space-between; align-items:flex-start;">
    <div>
        <h1>RAPPORTINO DI INTERVENTO</h1>
        <p style="font-size:10px; color:#6b7280;">N. {{ $intervento->numero }}</p>
    </div>
    <div style="text-align:right; font-size:10px;">
        <p>Data: <strong>{{ $intervento->data_pianificata?->format('d/m/Y') ?? now()->format('d/m/Y') }}</strong></p>
        <p>Stato: <strong>{{ $intervento->stato_label }}</strong></p>
        <p>Priorità: <strong>{{ $intervento->priorita_label }}</strong></p>
    </div>
</div>

<div class="separatore"></div>

<div class="grid-2">
    <div class="col">
        <div class="campo">
            <label>Cliente</label>
            <p><strong>{{ $intervento->cliente->ragione_sociale }}</strong></p>
            <p>{{ $intervento->cliente->indirizzo_completo }}</p>
            <p>{{ $intervento->cliente->telefono }}</p>
        </div>
        <div class="campo">
            <label>Tecnico assegnato</label>
            <p>{{ $intervento->tecnico?->name ?? 'Non assegnato' }}</p>
        </div>
    </div>
    <div class="col">
        <div class="campo">
            <label>Titolo intervento</label>
            <p><strong>{{ $intervento->titolo }}</strong></p>
        </div>
        <div class="campo">
            <label>Descrizione</label>
            <p>{{ $intervento->descrizione ?? '—' }}</p>
        </div>
        @if($intervento->minuti_lavorati > 0)
        <div class="campo">
            <label>Tempo lavorato</label>
            <p>{{ intdiv($intervento->minuti_lavorati, 60) }}h {{ $intervento->minuti_lavorati % 60 }}min</p>
        </div>
        @endif
    </div>
</div>

@if($intervento->checklist->count() > 0)
<div>
    <strong>Checklist attività:</strong>
    <div class="checklist">
        @foreach($intervento->checklist as $item)
        <div class="checklist-item">
            <span class="check {{ $item->completata ? 'done' : '' }}">{{ $item->completata ? '✓' : '' }}</span>
            {{ $item->voce }}
            @if($item->completata && $item->completata_da)
                <small style="margin-left:auto; color:#6b7280;">{{ $item->completata_il?->format('d/m/Y H:i') }}</small>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@if($intervento->articoli->count() > 0)
<div style="margin-top:15px;">
    <strong>Ricambi e materiali utilizzati:</strong>
    <table>
        <thead>
            <tr><th>Descrizione</th><th>Q.tà</th><th>U.M.</th><th style="text-align:right">Prezzo Unit.</th><th style="text-align:right">Totale</th></tr>
        </thead>
        <tbody>
            @foreach($intervento->articoli as $ia)
            <tr>
                <td>{{ $ia->articolo->descrizione }}</td>
                <td>{{ number_format($ia->quantita, 2, ',', '.') }}</td>
                <td>{{ $ia->articolo->unita_misura }}</td>
                <td style="text-align:right">€ {{ number_format($ia->prezzo_unitario, 2, ',', '.') }}</td>
                <td style="text-align:right">€ {{ number_format($ia->quantita * $ia->prezzo_unitario, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($intervento->note_cliente)
<div style="margin-top:15px; background:#f9fafb; border:1px solid #e5e7eb; padding:8px 12px; border-radius:4px;">
    <strong>Note per il cliente:</strong><br>
    {{ $intervento->note_cliente }}
</div>
@endif

<div class="firma-section">
    <div class="firma-box">
        <div class="firma-col">
            @if($intervento->firma_cliente)
                <img src="{{ storage_path('app/public/' . $intervento->firma_cliente) }}" style="max-height:80px; border:1px solid #e5e7eb;">
            @endif
            <div class="firma-line">Firma Cliente</div>
        </div>
        <div class="firma-col">
            <div class="firma-line">Firma Tecnico: {{ $intervento->tecnico?->name }}</div>
        </div>
    </div>
</div>
</body>
</html>
