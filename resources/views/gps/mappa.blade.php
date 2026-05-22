@extends('layouts.app')
@section('titolo', 'Mappa Tecnici in Tempo Reale')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Posizione Tecnici in Tempo Reale</h5>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-success">{{ $tecnici->where('statoTecnico.disponibile', true)->count() }} online</span>
        <button onclick="aggiornaPosizioniManuale()" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
        </button>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div id="mappa" style="height: 500px; border-radius: 12px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">Tecnici Attivi</div>
            <div class="card-body p-0">
                @forelse($tecnici as $tecnico)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <img src="{{ $tecnico->avatar_url }}" alt="{{ $tecnico->name }}" class="rounded-circle me-3" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="fw-medium">{{ $tecnico->name }}</div>
                        @if($tecnico->statoTecnico?->disponibile)
                            <small class="text-success">
                                <i class="bi bi-circle-fill me-1" style="font-size:0.5rem"></i>Online
                                @if($tecnico->statoTecnico->ultima_posizione_il)
                                    · {{ $tecnico->statoTecnico->ultima_posizione_il->diffForHumans() }}
                                @endif
                            </small>
                        @else
                            <small class="text-muted">
                                <i class="bi bi-circle me-1" style="font-size:0.5rem"></i>Offline
                            </small>
                        @endif
                    </div>
                    @if($tecnico->statoTecnico?->disponibile)
                    <a href="{{ route('gps.storico', $tecnico) }}" class="btn btn-xs btn-outline-secondary btn-sm">
                        <i class="bi bi-clock-history"></i>
                    </a>
                    @endif
                </div>
                @empty
                <div class="text-center p-4 text-muted">
                    <i class="bi bi-geo-alt-fill fs-3 d-block mb-2"></i>
                    Nessun tecnico online
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=inizializzaMappa" async defer></script>
<script>
const tecniciData = @json($tecnici->map(fn($t) => [
    'id' => $t->id,
    'nome' => $t->name,
    'disponibile' => $t->statoTecnico?->disponibile ?? false,
    'lat' => $t->statoTecnico?->ultima_lat,
    'lng' => $t->statoTecnico?->ultima_lng,
    'aggiornato' => $t->statoTecnico?->ultima_posizione_il?->diffForHumans(),
]));

let mappa;
const markers = {};

function inizializzaMappa() {
    // Centro Italia come default
    const centroItalia = { lat: 41.9028, lng: 12.4964 };
    mappa = new google.maps.Map(document.getElementById('mappa'), {
        zoom: 7,
        center: centroItalia,
        mapTypeId: 'roadmap',
        styles: [{ featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }]
    });

    tecniciData.forEach(t => {
        if (t.lat && t.lng && t.disponibile) {
            aggiungiMarker(t);
        }
    });
}

function aggiungiMarker(tecnico) {
    const marker = new google.maps.Marker({
        position: { lat: parseFloat(tecnico.lat), lng: parseFloat(tecnico.lng) },
        map: mappa,
        title: tecnico.nome,
        icon: {
            url: 'data:image/svg+xml,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="50" viewBox="0 0 40 50">
                    <circle cx="20" cy="20" r="18" fill="#2563eb" stroke="white" stroke-width="3"/>
                    <text x="20" y="26" text-anchor="middle" fill="white" font-size="18" font-weight="bold">${tecnico.nome.charAt(0).toUpperCase()}</text>
                    <polygon points="15,36 25,36 20,48" fill="#2563eb"/>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 50),
        }
    });

    const info = new google.maps.InfoWindow({
        content: `<div style="padding:8px">
            <strong>${tecnico.nome}</strong><br>
            <small class="text-muted">Aggiornato: ${tecnico.aggiornato || '—'}</small>
        </div>`
    });

    marker.addListener('click', () => info.open(mappa, marker));
    markers[tecnico.id] = marker;
}

function aggiornaPosizioniManuale() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => window.location.reload());
}

// Ricarica ogni 60 secondi
setInterval(() => window.location.reload(), 60000);
</script>
@endpush
@endsection
