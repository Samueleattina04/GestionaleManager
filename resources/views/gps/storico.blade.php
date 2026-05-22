@extends('layouts.app')
@section('titolo', 'Storico GPS')
@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Storico posizioni GPS</h1>
    <a href="{{ route('gps.mappa') }}" class="btn btn-outline-primary">
        <i class="bi bi-map me-1"></i>Mappa live
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tecnico</label>
                <select name="tecnico_id" class="form-select">
                    <option value="">Tutti i tecnici</option>
                    @foreach($tecnici as $t)
                    <option value="{{ $t->id }}" {{ request('tecnico_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data</label>
                <input type="date" name="data" value="{{ request('data', today()->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Cerca</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><strong>Percorso sulla mappa</strong></div>
            <div class="card-body p-0">
                <div id="mappa" style="height:420px;border-radius:0 0 .375rem .375rem"></div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><strong>Registro posizioni ({{ $posizioni->count() }})</strong></div>
            <div style="max-height:420px;overflow-y:auto">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Orario</th>
                            <th>Tecnico</th>
                            <th>Coordinate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posizioni as $pos)
                        <tr>
                            <td>{{ $pos->rilevato_il?->format('H:i:s') }}</td>
                            <td>{{ $pos->user?->name }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ number_format($pos->latitudine, 4) }},
                                    {{ number_format($pos->longitudine, 4) }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Nessuna posizione registrata.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const posizioni = @json($posizioni->map(fn($p) => ['lat' => (float)$p->latitudine, 'lng' => (float)$p->longitudine, 'nome' => $p->user?->name, 'ora' => $p->rilevato_il?->format('H:i')]));

function initMappa() {
    if (!posizioni.length) return;
    const centro = { lat: posizioni[0].lat, lng: posizioni[0].lng };
    const map = new google.maps.Map(document.getElementById('mappa'), { zoom: 13, center: centro });

    if (posizioni.length > 1) {
        const path = new google.maps.Polyline({
            path: posizioni,
            geodesic: true,
            strokeColor: '#2563eb',
            strokeOpacity: 0.8,
            strokeWeight: 3
        });
        path.setMap(map);
    }

    posizioni.forEach((p, i) => {
        new google.maps.Marker({
            position: p,
            map,
            title: `${p.nome} - ${p.ora}`,
            icon: i === posizioni.length - 1 ? 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });
    });
}
</script>
@if(config('services.google_maps.key'))
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMappa"></script>
@else
<script>document.getElementById('mappa').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><span><i class="bi bi-map me-2"></i>Configura Google Maps API per visualizzare la mappa.</span></div>';</script>
@endif
@endpush
@endsection
