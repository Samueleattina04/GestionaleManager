@extends('layouts.app')
@section('titolo', 'Report magazzino')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Report magazzino</h1>
    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Indietro
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="text-muted">Valore totale magazzino</h5>
                <h2 class="text-primary">€ {{ number_format($valore_totale ?? 0, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h5 class="text-muted">Articoli sotto scorta</h5>
                <h2 class="text-danger">{{ $sotto_scorta ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="text-muted">Movimenti questo mese</h5>
                <h2 class="text-success">{{ $movimenti_mese ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Valore per categoria</strong></div>
            <div class="card-body"><canvas id="chartCategorie" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Top 10 articoli per valore</strong></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Articolo</th>
                            <th class="text-end">Quantità</th>
                            <th class="text-end">Valore</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($top_articoli ?? [] as $a)
                        <tr>
                            <td>{{ $a->nome }}</td>
                            <td class="text-end">{{ number_format($a->quantita_totale ?? 0, 2) }} {{ $a->unita_misura }}</td>
                            <td class="text-end">€ {{ number_format(($a->quantita_totale ?? 0) * ($a->prezzo_acquisto ?? 0), 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Nessun dato.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const categorie = @json($categorie ?? []);
new Chart(document.getElementById('chartCategorie'), {
    type: 'doughnut',
    data: {
        labels: categorie.map(c => c.nome || 'Senza categoria'),
        datasets: [{ data: categorie.map(c => c.valore || 0), backgroundColor: ['#2563eb','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#84cc16'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right' } } }
});
</script>
@endpush
@endsection
