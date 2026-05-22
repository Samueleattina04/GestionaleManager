<?xml version="1.0" encoding="UTF-8"?>
<p:FatturaElettronica versione="FPR12" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">
  <FatturaElettronicaHeader>
    <DatiTrasmissione>
      <IdTrasmittente>
        <IdPaese>IT</IdPaese>
        <IdCodice>{{ preg_replace('/[^0-9]/', '', $tenant->partita_iva ?? '00000000000') }}</IdCodice>
      </IdTrasmittente>
      <ProgressivoInvio>{{ str_pad($fattura->id, 5, '0', STR_PAD_LEFT) }}</ProgressivoInvio>
      <FormatoTrasmissione>FPR12</FormatoTrasmissione>
      <CodiceDestinatario>{{ $fattura->cliente?->codice_sdi ?? '0000000' }}</CodiceDestinatario>
      @if($fattura->cliente?->pec)
      <PECDestinatario>{{ $fattura->cliente->pec }}</PECDestinatario>
      @endif
    </DatiTrasmissione>
    <CedentePrestatore>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>{{ preg_replace('/[^0-9]/', '', $tenant->partita_iva ?? '00000000000') }}</IdCodice>
        </IdFiscaleIVA>
        @if($tenant->codice_fiscale)
        <CodiceFiscale>{{ $tenant->codice_fiscale }}</CodiceFiscale>
        @endif
        <Anagrafica>
          <Denominazione>{{ htmlspecialchars($tenant->ragione_sociale) }}</Denominazione>
        </Anagrafica>
        <RegimeFiscale>RF01</RegimeFiscale>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>{{ htmlspecialchars($tenant->indirizzo ?? 'Via Roma 1') }}</Indirizzo>
        <CAP>{{ $tenant->cap ?? '00100' }}</CAP>
        <Comune>{{ htmlspecialchars($tenant->citta ?? 'Roma') }}</Comune>
        @if($tenant->provincia)
        <Provincia>{{ $tenant->provincia }}</Provincia>
        @endif
        <Nazione>IT</Nazione>
      </Sede>
    </CedentePrestatore>
    @if($fattura->cliente)
    <CessionarioCommittente>
      <DatiAnagrafici>
        @if($fattura->cliente->partita_iva)
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>{{ preg_replace('/[^0-9]/', '', $fattura->cliente->partita_iva) }}</IdCodice>
        </IdFiscaleIVA>
        @endif
        @if($fattura->cliente->codice_fiscale)
        <CodiceFiscale>{{ $fattura->cliente->codice_fiscale }}</CodiceFiscale>
        @endif
        <Anagrafica>
          <Denominazione>{{ htmlspecialchars($fattura->cliente->ragione_sociale) }}</Denominazione>
        </Anagrafica>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>{{ htmlspecialchars($fattura->cliente->indirizzo ?? 'Via Roma 1') }}</Indirizzo>
        <CAP>{{ $fattura->cliente->cap ?? '00100' }}</CAP>
        <Comune>{{ htmlspecialchars($fattura->cliente->citta ?? 'Roma') }}</Comune>
        @if($fattura->cliente->provincia)
        <Provincia>{{ $fattura->cliente->provincia }}</Provincia>
        @endif
        <Nazione>{{ $fattura->cliente->paese ?? 'IT' }}</Nazione>
      </Sede>
    </CessionarioCommittente>
    @endif
  </FatturaElettronicaHeader>
  <FatturaElettronicaBody>
    <DatiGenerali>
      <DatiGeneraliDocumento>
        <TipoDocumento>{{ $fattura->tipo === 'nota_credito' ? 'TD04' : 'TD01' }}</TipoDocumento>
        <Divisa>EUR</Divisa>
        <Data>{{ $fattura->data->format('Y-m-d') }}</Data>
        <Numero>{{ $fattura->numero }}</Numero>
      </DatiGeneraliDocumento>
    </DatiGenerali>
    <DatiBeniServizi>
      @foreach($fattura->righe as $idx => $riga)
      <DettaglioLinee>
        <NumeroLinea>{{ $idx + 1 }}</NumeroLinea>
        <Descrizione>{{ htmlspecialchars(Str::limit($riga->descrizione, 1000)) }}</Descrizione>
        <Quantita>{{ number_format($riga->quantita, 2, '.', '') }}</Quantita>
        <UnitaMisura>{{ $riga->unita_misura }}</UnitaMisura>
        <PrezzoUnitario>{{ number_format($riga->prezzo_unitario, 2, '.', '') }}</PrezzoUnitario>
        @if($riga->sconto > 0)
        <ScontoMaggiorazione>
          <Tipo>SC</Tipo>
          <Percentuale>{{ number_format($riga->sconto, 2, '.', '') }}</Percentuale>
        </ScontoMaggiorazione>
        @endif
        <PrezzoTotale>{{ number_format($riga->totale / (1 + $riga->iva / 100), 2, '.', '') }}</PrezzoTotale>
        <AliquotaIVA>{{ number_format($riga->iva, 2, '.', '') }}</AliquotaIVA>
      </DettaglioLinee>
      @endforeach
      @php $aliquote = $fattura->righe->groupBy('iva'); @endphp
      @foreach($aliquote as $aliquota => $righe)
      <DatiRiepilogo>
        <AliquotaIVA>{{ number_format($aliquota, 2, '.', '') }}</AliquotaIVA>
        <ImponibileImporto>{{ number_format($righe->sum(fn($r) => $r->totale / (1 + $r->iva / 100)), 2, '.', '') }}</ImponibileImporto>
        <Imposta>{{ number_format($righe->sum(fn($r) => $r->totale - $r->totale / (1 + $r->iva / 100)), 2, '.', '') }}</Imposta>
        <EsigibilitaIVA>I</EsigibilitaIVA>
      </DatiRiepilogo>
      @endforeach
    </DatiBeniServizi>
    <DatiPagamento>
      <CondizioniPagamento>TP02</CondizioniPagamento>
      <DettaglioPagamento>
        <ModalitaPagamento>{{ $fattura->modalita_pagamento ? 'MP05' : 'MP05' }}</ModalitaPagamento>
        @if($fattura->data_scadenza)
        <DataScadenzaPagamento>{{ $fattura->data_scadenza->format('Y-m-d') }}</DataScadenzaPagamento>
        @endif
        <ImportoPagamento>{{ number_format($fattura->totale, 2, '.', '') }}</ImportoPagamento>
      </DettaglioPagamento>
    </DatiPagamento>
  </FatturaElettronicaBody>
</p:FatturaElettronica>
