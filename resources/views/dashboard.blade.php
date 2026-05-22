@extends('layouts.app')
@section('titolo', 'Dashboard')

@section('contenuto')
<div class="row g-3 mb-4">
    <!-- Fatturato mese -->
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:0.8rem">FATTURATO MESE</span>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-graph-up text-primary"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">€ {{ number_format($fatturatoMese, 2, ',', '.') }}</h3>
                @php
                    $variazione = $fatturatoMesePrec > 0
                        ? (($fatturatoMese - $fatturatoMesePrec) / $fatturatoMesePrec) * 100
                        : 0;
                @endphp
                <small class="{{ $variazione >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="bi bi-arrow-{{ $variazione >= 0 ? 'up' : 'down' }}"></i>
                    {{ number_format(abs($variazione), 1) }}% vs mese scorso
                </small>
            </div>
        </div>
    </div>

    <!-- Interventi aperti -->
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:0.8rem">INTERVENTI</span>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-tools text-warning"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $interventiAperti + $interventiInCorso }}</h3>
                <small class="text-muted">
                    <span class="badge bg-warning text-dark">{{ $interventiAperti }} da assegnare</span>
                    <span class="badge bg-info ms-1">{{ $interventiInCorso }} in corso</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Completati oggi -->
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:0.8rem">COMPLETATI OGGI</span>
                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $interventiOggi }}</h3>
                <small class="text-muted">Interventi completati oggi</small>
            </div>
        </div>
    </div>

    <!-- Sotto scorta -->
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:0.8rem">SOTTO SCORTA</span>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $sottoScorta }}</h3>
                <a href="{{ route('magazzino.sotto-scorta') }}" class="small text-danger text-decoration-none">
                    Articoli in esaurimento →
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Grafico fatturato -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Andamento Fatturato (ultimi 6 mesi)</span>
            </div>
            <div class="card-body">
                <canvas id="graficoFatturato" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Tecnici attivi -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Tecnici Attivi</span>
                <a href="{{ route('gps.mappa') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-map me-1"></i>Mappa
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($tecniciAttivi as $tecnico)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <img src="{{ $tecnico->avatar_url }}" alt="{{ $tecnico->name }}" class="rounded-circle me-3" width="36" height="36">
                        <div class="flex-grow-1">
                            <div class="fw-medium" style="font-size:0.875rem">{{ $tecnico->name }}</div>
                            @if($tecnico->statoTecnico?->ultima_posizione_il)
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $tecnico->statoTecnico->ultima_posizione_il->diffForHumans() }}
                                </small>
                            @endif
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill">Online</span>
                    </div>
                @empty
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-person-slash fs-3 d-block mb-2"></i>
                        Nessun tecnico attivo
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Ultimi interventi -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Ultimi Interventi</span>
                <a href="{{ route('interventi.index') }}" class="btn btn-sm btn-outline-primary">Tutti →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                        @forelse($ultimiInterventi as $intervento)
                            <tr>
                                <td>
                                    <a href="{{ route('interventi.show', $intervento) }}" class="text-decoration-none fw-medium">
                                        {{ $intervento->titolo }}
                                    </a>
                                    <div class="text-muted" style="font-size:0.8rem">{{ $intervento->cliente->ragione_sociale }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-stato-{{ $intervento->stato }} rounded-pill">
                                        {{ $intervento->stato_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-priorita-{{ $intervento->priorita }} rounded-pill">
                                        {{ $intervento->priorita_label }}
                                    </span>
                                </td>
                                <td class="text-muted" style="font-size:0.8rem">
                                    {{ $intervento->tecnico?->name ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Nessun intervento presente</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scadenze -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Prossime Scadenze Fatture</span>
                <a href="{{ route('fatture.scadenzario') }}" class="btn btn-sm btn-outline-primary">Tutte →</a>
            </div>
            <div class="card-body p-0">
                @forelse($scadenze as $fattura)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium" style="font-size:0.875rem">
                                {{ $fattura->cliente?->ragione_sociale ?? '—' }}
                            </div>
                            <small class="text-muted">Fatt. {{ $fattura->numero }}/{{ $fattura->anno }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold text-danger">€ {{ number_format($fattura->saldo, 2, ',', '.') }}</div>
                            <small class="{{ $fattura->data_scadenza->isPast() ? 'text-danger' : 'text-muted' }}">
                                {{ $fattura->data_scadenza->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                        Nessuna scadenza imminente
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoFatturato');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($graficoDati, 'mese')) !!},
        datasets: [{
            label: 'Fatturato (€)',
            data: {!! json_encode(array_column($graficoDati, 'totale')) !!},
            backgroundColor: 'rgba(37, 99, 235, 0.8)',
            borderColor: '#2563eb',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '€ ' + value.toLocaleString('it-IT')
                }
            }
        }
    }
});
</script>
@endpush
