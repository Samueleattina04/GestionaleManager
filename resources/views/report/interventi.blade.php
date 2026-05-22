@extends('layouts.app')
@section('titolo', 'Report interventi')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Report interventi</h1>
    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Da data</label>
                <input type="date" name="da" value="{{ request('da', now()->startOfMonth()->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">A data</label>
                <input type="date" name="a" value="{{ request('a', now()->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tecnico</label>
                <select name="tecnico_id" class="form-select">
                    <option value="">Tutti i tecnici</option>
                    @foreach($tecnici ?? [] as $t)
                    <option value="{{ $t->id }}" {{ request('tecnico_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Aggiorna</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    @php
    $stati = ['da_assegnare' => ['label' => 'Da assegnare', 'color' => 'warning'], 'assegnato' => ['label' => 'Assegnato', 'color' => 'info'], 'in_corso' => ['label' => 'In corso', 'color' => 'primary'], 'completato' => ['label' => 'Completato', 'color' => 'success'], 'annullato' => ['label' => 'Annullato', 'color' => 'secondary']];
    @endphp
    @foreach($stati as $stato => $info)
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-2">
                <h4 class="text-{{ $info['color'] }}">{{ $conteggiStati[$stato] ?? 0 }}</h4>
                <small class="text-muted">{{ $info['label'] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>Interventi per mese</strong></div>
            <div class="card-body"><canvas id="chartInterventi" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>Distribuzione stati</strong></div>
            <div class="card-body"><canvas id="chartStati" height="120"></canvas></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Performance tecnici</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tecnico</th>
                    <th class="text-center">Totale</th>
                    <th class="text-center">Completati</th>
                    <th class="text-center">% completamento</th>
                    <th class="text-center">Media ore</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tecnici ?? [] as $t)
                @php $completati = $t->interventi_completati ?? 0; $totale = $t->interventi_totali ?? 0; @endphp
                <tr>
                    <td>{{ $t->name }}</td>
                    <td class="text-center">{{ $totale }}</td>
                    <td class="text-center">{{ $completati }}</td>
                    <td class="text-center">
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-success" style="width:{{ $totale > 0 ? round($completati/$totale*100) : 0 }}%"></div>
                        </div>
                        <small>{{ $totale > 0 ? round($completati/$totale*100) : 0 }}%</small>
                    </td>
                    <td class="text-center">{{ $t->media_minuti ? round($t->media_minuti/60, 1) . 'h' : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Nessun dato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const mesi = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
const datiMese = @json($datiMese ?? []);
new Chart(document.getElementById('chartInterventi'), {
    type: 'line',
    data: {
        labels: datiMese.map(d => mesi[(d.mese||1)-1]),
        datasets: [{ label: 'Interventi', data: datiMese.map(d => d.count||0), borderColor: '#2563eb', tension: 0.3, fill: true, backgroundColor: 'rgba(37,99,235,0.1)' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

const statiLabels = @json(array_column(array_values($stati ?? []), 'label'));
const statiData = @json(array_values($conteggiStati ?? []));
new Chart(document.getElementById('chartStati'), {
    type: 'doughnut',
    data: {
        labels: statiLabels,
        datasets: [{ data: statiData, backgroundColor: ['#fbbf24','#60a5fa','#2563eb','#22c55e','#9ca3af'] }]
    },
    options: { responsive: true }
});
</script>
@endpush
@endsection
