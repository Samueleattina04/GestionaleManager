<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 0; }
    .intestazione { display: flex; justify-content: space-between; margin-bottom: 30px; }
    .logo-azienda h2 { font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 0; }
    .logo-azienda p { font-size: 10px; color: #6b7280; margin: 2px 0; }
    .dati-fattura { text-align: right; }
    .dati-fattura h1 { font-size: 22px; color: #1e3a8a; margin: 0; }
    .dati-fattura .numero { font-size: 14px; font-weight: bold; }
    .separatore { border-top: 2px solid #1e3a8a; margin: 15px 0; }
    .sezione-indirizzi { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .indirizzo-box { width: 48%; }
    .indirizzo-box h4 { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #6b7280; margin-bottom: 5px; letter-spacing: 1px; }
    .indirizzo-box p { margin: 2px 0; line-height: 1.5; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table thead { background-color: #1e3a8a; color: white; }
    table thead th { padding: 8px 10px; text-align: left; font-size: 10px; }
    table tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
    table tbody tr:nth-child(even) { background-color: #f9fafb; }
    .totali { margin-top: 20px; }
    .totali table { width: 300px; margin-left: auto; }
    .totali td { padding: 4px 10px; }
    .totali .totale-finale { font-weight: bold; font-size: 13px; background: #1e3a8a; color: white; }
    .footer { margin-top: 40px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    .badge-stato { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .note-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; margin-top: 15px; border-radius: 4px; }
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
        @if($tenant->pec)<p>PEC: {{ $tenant->pec }}</p>@endif
        @if($tenant->codice_sdi)<p>Codice SDI: {{ $tenant->codice_sdi }}</p>@endif
    </div>
    <div class="dati-fattura">
        <h1>{{ strtoupper(str_replace('_', ' ', $fattura->tipo)) }}</h1>
        <p class="numero">N. {{ $fattura->numero }}</p>
        <p>Data: <strong>{{ $fattura->data->format('d/m/Y') }}</strong></p>
        @if($fattura->data_scadenza)
        <p>Scadenza: <strong>{{ $fattura->data_scadenza->format('d/m/Y') }}</strong></p>
        @endif
        @if($fattura->modalita_pagamento)
        <p>Pagamento: {{ $fattura->modalita_pagamento }}</p>
        @endif
    </div>
</div>

<div class="separatore"></div>

<div class="sezione-indirizzi">
    <div class="indirizzo-box">
        <h4>Emittente</h4>
        <p><strong>{{ $tenant->ragione_sociale }}</strong></p>
        <p>{{ $tenant->indirizzo }}</p>
        <p>{{ $tenant->cap }} {{ $tenant->citta }} {{ $tenant->provincia }}</p>
        <p>P.IVA: {{ $tenant->partita_iva }}</p>
    </div>
    <div class="indirizzo-box">
        <h4>Destinatario</h4>
        @if($fattura->cliente)
        <p><strong>{{ $fattura->cliente->ragione_sociale }}</strong></p>
        <p>{{ $fattura->cliente->indirizzo }}</p>
        <p>{{ $fattura->cliente->cap }} {{ $fattura->cliente->citta }} {{ $fattura->cliente->provincia }}</p>
        @if($fattura->cliente->partita_iva)<p>P.IVA: {{ $fattura->cliente->partita_iva }}</p>@endif
        @if($fattura->cliente->codice_fiscale)<p>C.F.: {{ $fattura->cliente->codice_fiscale }}</p>@endif
        @if($fattura->cliente->codice_sdi)<p>Cod. SDI: {{ $fattura->cliente->codice_sdi }}</p>@endif
        @elseif($fattura->fornitore)
        <p><strong>{{ $fattura->fornitore->ragione_sociale }}</strong></p>
        <p>{{ $fattura->fornitore->indirizzo }}</p>
        @endif
    </div>
</div>

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
        @foreach($fattura->righe as $riga)
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
            <td>Imponibile totale:</td>
            <td style="text-align:right">€ {{ number_format($fattura->totale_imponibile, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>IVA totale:</td>
            <td style="text-align:right">€ {{ number_format($fattura->totale_iva, 2, ',', '.') }}</td>
        </tr>
        @if($fattura->sconto_globale > 0)
        <tr>
            <td>Sconto globale {{ $fattura->sconto_globale }}%:</td>
            <td style="text-align:right">- € {{ number_format($fattura->totale * ($fattura->sconto_globale/100), 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="totale-finale">
            <td style="padding: 8px 10px;"><strong>TOTALE FATTURA:</strong></td>
            <td style="text-align:right; padding: 8px 10px;"><strong>€ {{ number_format($fattura->totale, 2, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>

@if($fattura->note)
<div class="note-box">
    <strong>Note:</strong><br>{{ $fattura->note }}
</div>
@endif

<div class="footer">
    {{ $tenant->ragione_sociale }} - {{ $tenant->indirizzo }}, {{ $tenant->citta }}
    @if($tenant->partita_iva) - P.IVA: {{ $tenant->partita_iva }}@endif
    @if($tenant->email) - {{ $tenant->email }}@endif
    @if($tenant->telefono) - Tel: {{ $tenant->telefono }}@endif
</div>
</body>
</html>
