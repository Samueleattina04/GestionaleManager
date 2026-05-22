@extends('layouts.app')
@section('titolo', 'Articoli sotto scorta')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Articoli sotto scorta</h1>
    <a href="{{ route('magazzini.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Magazzini
    </a>
</div>

@if($articoli->count() > 0)
<div class="alert alert-danger d-flex align-items-center mb-4">
    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
    <div>
        <strong>{{ $articoli->count() }} articol{{ $articoli->count() === 1 ? 'o' : 'i' }} sotto la scorta minima.</strong>
        Verificare il livello delle scorte e procedere con i rifornimenti necessari.
    </div>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill me-2"></i>
    Tutti gli articoli sono sopra la scorta minima.
</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Codice</th>
                    <th>Articolo</th>
                    <th>Magazzino</th>
                    <th class="text-center">Scorta min.</th>
                    <th class="text-center">Quantità attuale</th>
                    <th class="text-center">Differenza</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($articoli as $item)
                @php
                    $differenza = ($item->giacenza?->quantita ?? 0) - ($item->scorta_minima ?? 0);
                @endphp
                <tr>
                    <td><code>{{ $item->codice }}</code></td>
                    <td><strong>{{ $item->nome }}</strong></td>
                    <td>{{ $item->magazzino?->nome ?? 'Tutti i magazzini' }}</td>
                    <td class="text-center">{{ $item->scorta_minima ?? 0 }} {{ $item->unita_misura }}</td>
                    <td class="text-center">{{ number_format($item->giacenza?->quantita ?? 0, 2) }} {{ $item->unita_misura }}</td>
                    <td class="text-center">
                        <span class="fw-bold text-danger">{{ number_format($differenza, 2) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('articoli.show', $item) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nessun articolo sotto scorta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
