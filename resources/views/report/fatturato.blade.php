@extends('layouts.app')
@section('titolo', 'Report fatturato')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Report fatturato {{ $anno }}</h1>
    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Anno</label>
                <select name="anno" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $anno == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Aggiorna</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="text-muted">Totale fatturato</h5>
                <h2 class="text-primary">€ {{ number_format($totale_fatturato ?? 0, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="text-muted">Totale incassato</h5>
                <h2 class="text-success">€ {{ number_format($totale_incassato ?? 0, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h5 class="text-muted">Da incassare</h5>
                <h2 class="text-warning">€ {{ number_format(($totale_fatturato ?? 0) - ($totale_incassato ?? 0), 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><strong>Andamento mensile</strong></div>
    <div class="card-body">
        <canvas id="chartFatturato" height="80"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Dettaglio mensile</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mese</th>
                    <th class="text-end">Fatturato</th>
                    <th class="text-end">Incassato</th>
                    <th class="text-end">Differenza</th>
                    <th class="text-end">N. fatture</th>
                </tr>
            </thead>
            <tbody>
                @php
                $mesi = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
                @endphp
                @foreach($dati as $d)
                <tr>
                    <td>{{ $mesi[($d['mese'] ?? 1) - 1] }}</td>
                    <td class="text-end">€ {{ number_format($d['fatturato'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end">€ {{ number_format($d['incassato'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end text-{{ ($d['fatturato'] ?? 0) - ($d['incassato'] ?? 0) > 0 ? 'warning' : 'success' }}">
                        € {{ number_format(($d['fatturato'] ?? 0) - ($d['incassato'] ?? 0), 2, ',', '.') }}
                    </td>
                    <td class="text-end">{{ $d['count'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const mesi = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
const dati = @json($dati);
const labels = dati.map(d => mesi[(d.mese || 1) - 1]);
const fatturato = dati.map(d => d.fatturato || 0);
const incassato = dati.map(d => d.incassato || 0);

new Chart(document.getElementById('chartFatturato'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Fatturato', data: fatturato, backgroundColor: 'rgba(37,99,235,0.7)' },
            { label: 'Incassato', data: incassato, backgroundColor: 'rgba(34,197,94,0.7)' }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endpush
@endsection
