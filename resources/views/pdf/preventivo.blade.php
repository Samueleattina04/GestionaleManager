<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 0; }
    .intestazione { display: flex; justify-content: space-between; margin-bottom: 30px; }
    .logo-azienda h2 { font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 0; }
    .logo-azienda p { font-size: 10px; color: #6b7280; margin: 2px 0; }
    .dati-doc { text-align: right; }
    .dati-doc h1 { font-size: 22px; color: #1e3a8a; margin: 0; }
    .separatore { border-top: 2px solid #1e3a8a; margin: 15px 0; }
    .sezione-indirizzi { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .indirizzo-box { width: 48%; }
    .indirizzo-box h4 { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #6b7280; margin-bottom: 5px; letter-spacing: 1px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table thead { background-color: #1e3a8a; color: white; }
    table thead th { padding: 8px 10px; text-align: left; font-size: 10px; }
    table tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
    .totali { margin-top: 20px; }
    .totali table { width: 300px; margin-left: auto; }
    .totali td { padding: 4px 10px; }
    .totale-finale { font-weight: bold; font-size: 13px; background: #1e3a8a; color: white; }
    .validita-box { background: #fef3c7; border: 1px solid #f59e0b; padding: 8px 12px; border-radius: 4px; margin-top: 15px; font-size: 10px; }
    .footer { margin-top: 40px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
</style>
</head>
<body>
<div class="intestazione">
    <div class="logo-azienda">
        <h2>{{ $tenant->ragione_sociale }}</h2>
        <p>{{ $tenant->indirizzo }}</p>
        <p>{{ $tenant->cap }} {{ $tenant->citta }} {{ $tenant->provincia ? '('.$tenant->provincia.')' : '' }}</p>
        @if($tenant->partita_iva)<p>P.IVA: {{ $tenant->partita_iva }}</p>@endif
        @if($tenant->email)<p>Email: {{ $tenant->email }}</p>@endif
    </div>
    <div class="dati-doc">
        <h1>PREVENTIVO</h1>
        <p><strong>N. {{ $preventivo->numero }}</strong></p>
        <p>Data: {{ $preventivo->data->format('d/m/Y') }}</p>
        @if($preventivo->data_scadenza)
        <p>Valido fino al: <strong>{{ $preventivo->data_scadenza->format('d/m/Y') }}</strong></p>
        @endif
    </div>
</div>

<div class="separatore"></div>

<div class="sezione-indirizzi">
    <div class="indirizzo-box">
        <h4>Emittente</h4>
        <p><strong>{{ $tenant->ragione_sociale }}</strong></p>
        <p>{{ $tenant->indirizzo }}</p>
        <p>{{ $tenant->cap }} {{ $tenant->citta }}</p>
        <p>P.IVA: {{ $tenant->partita_iva }}</p>
    </div>
    <div class="indirizzo-box">
        <h4>Destinatario</h4>
        <p><strong>{{ $preventivo->cliente->ragione_sociale }}</strong></p>
        <p>{{ $preventivo->cliente->indirizzo }}</p>
        <p>{{ $preventivo->cliente->cap }} {{ $preventivo->cliente->citta }}</p>
        @if($preventivo->cliente->partita_iva)<p>P.IVA: {{ $preventivo->cliente->partita_iva }}</p>@endif
    </div>
</div>

@if($preventivo->oggetto)
<div style="background:#f0f9ff; border-left:3px solid #1e3a8a; padding:8px 12px; margin-bottom:15px;">
    <strong>Oggetto:</strong> {{ $preventivo->oggetto }}
</div>
@endif

<table>
    <thead>
        <tr>
            <th style="width:50%">Descrizione</th>
            <th style="width:8%">Q.tà</th>
            <th style="width:8%">U.M.</th>
            <th style="width:12%">Prezzo Unit.</th>
            <th style="width:8%">Sc.%</th>
            <th style="width:6%">IVA%</th>
            <th style="width:12%; text-align:right">Totale</th>
        </tr>
    </thead>
    <tbody>
        @foreach($preventivo->righe as $riga)
        <tr>
            <td>{{ $riga->descrizione }}</td>
            <td>{{ number_format($riga->quantita, 2, ',', '.') }}</td>
            <td>{{ $riga->unita_misura }}</td>
            <td>€ {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}</td>
            <td>{{ $riga->sconto > 0 ? number_format($riga->sconto, 1).'%' : '—' }}</td>
            <td>{{ number_format($riga->iva, 0) }}%</td>
            <td style="text-align:right">€ {{ number_format($riga->totale, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totali">
    <table>
        <tr>
            <td>Imponibile:</td>
            <td style="text-align:right">€ {{ number_format($preventivo->totale_imponibile, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>IVA:</td>
            <td style="text-align:right">€ {{ number_format($preventivo->totale_iva, 2, ',', '.') }}</td>
        </tr>
        <tr class="totale-finale">
            <td style="padding:8px 10px;"><strong>TOTALE:</strong></td>
            <td style="text-align:right; padding:8px 10px;"><strong>€ {{ number_format($preventivo->totale, 2, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>

@if($preventivo->data_scadenza)
<div class="validita-box">
    ⚠ Questo preventivo è valido fino al <strong>{{ $preventivo->data_scadenza->format('d/m/Y') }}</strong>.
    Per accettare il preventivo contattaci entro tale data.
</div>
@endif

@if($preventivo->note)
<div style="margin-top:15px; font-size:10px;">
    <strong>Note:</strong><br>{{ $preventivo->note }}
</div>
@endif

@if($preventivo->condizioni)
<div style="margin-top:10px; font-size:9px; color:#6b7280;">
    <strong>Condizioni:</strong><br>{{ $preventivo->condizioni }}
</div>
@endif

<div class="footer">
    {{ $tenant->ragione_sociale }} - {{ $tenant->indirizzo }}, {{ $tenant->citta }}
    @if($tenant->partita_iva) - P.IVA: {{ $tenant->partita_iva }}@endif
    @if($tenant->email) - {{ $tenant->email }}@endif
</div>
</body>
</html>
