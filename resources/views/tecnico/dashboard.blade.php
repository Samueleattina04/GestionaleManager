@extends('layouts.app')
@section('titolo', 'Pannello Tecnico')

@section('contenuto')
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card border-0" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
            <div class="card-body text-white d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1">Ciao, {{ auth()->user()->name }}!</h5>
                    <p class="mb-0 opacity-75">
                        {{ now()->translatedFormat('l d F Y') }}
                    </p>
                </div>
                <div class="text-center">
                    <div x-data="{ disponibile: {{ $statoTecnico->disponibile ? 'true' : 'false' }} }">
                        <button @click="fetch('/tecnico/disponibilita', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}}).then(r=>r.json()).then(d=>disponibile=d.disponibile)"
                            :class="disponibile ? 'btn-success' : 'btn-outline-light'"
                            class="btn px-4 py-2 fw-semibold">
                            <i class="bi" :class="disponibile ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                            <span x-text="disponibile ? 'DISPONIBILE' : 'NON DISPONIBILE'"></span>
                        </button>
                        <p class="small mt-1 opacity-75 mb-0">Clicca per cambiare stato</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracciamento GPS -->
<div class="card mb-3" x-data="gpsTracker()">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-semibold mb-1"><i class="bi bi-geo-alt me-2 text-primary"></i>Tracciamento GPS</h6>
            <small class="text-muted" x-text="statoGps"></small>
        </div>
        <button @click="toggleGps()" :class="attivo ? 'btn-success' : 'btn-outline-secondary'" class="btn btn-sm">
            <i class="bi" :class="attivo ? 'bi-broadcast' : 'bi-broadcast-pin'"></i>
            <span x-text="attivo ? 'GPS Attivo' : 'Attiva GPS'"></span>
        </button>
    </div>
</div>

<!-- Interventi di oggi -->
<h6 class="fw-semibold mb-2">I miei interventi</h6>
<div class="row g-3">
    @forelse($interventiOggi as $intervento)
    <div class="col-12">
        <div class="card border-start border-4 border-{{ $intervento->priorita === 'urgente' ? 'danger' : ($intervento->priorita === 'normale' ? 'primary' : 'secondary') }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fw-semibold mb-0">{{ $intervento->titolo }}</h6>
                            <span class="badge badge-priorita-{{ $intervento->priorita }}">{{ $intervento->priorita_label }}</span>
                            <span class="badge badge-stato-{{ $intervento->stato }}">{{ $intervento->stato_label }}</span>
                        </div>
                        <p class="text-muted mb-1" style="font-size:0.875rem">
                            <i class="bi bi-building me-1"></i>{{ $intervento->cliente->ragione_sociale }}
                        </p>
                        @if($intervento->indirizzo_intervento ?? $intervento->cliente->indirizzo)
                        <p class="text-muted mb-1" style="font-size:0.875rem">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $intervento->indirizzo_intervento ?? $intervento->cliente->indirizzo_completo }}
                        </p>
                        @endif
                        @if($intervento->data_pianificata)
                        <p class="text-muted mb-0" style="font-size:0.875rem">
                            <i class="bi bi-clock me-1"></i>{{ $intervento->data_pianificata->format('H:i') }}
                        </p>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-1 ms-3">
                        <a href="{{ route('tecnico.interventi.dettaglio', $intervento) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i>Apri
                        </a>
                        @php $indirizzo = urlencode($intervento->indirizzo_intervento ?? $intervento->cliente->indirizzo_completo); @endphp
                        @if($indirizzo)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $indirizzo }}" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-map me-1"></i>Mappa
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center p-5 text-muted">
            <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
            <h5>Nessun intervento assegnato</h5>
            <p class="mb-0">Non hai interventi attivi al momento.</p>
        </div>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
function gpsTracker() {
    return {
        attivo: false,
        statoGps: 'GPS non attivo',
        intervallo: null,

        toggleGps() {
            if (this.attivo) {
                this.fermaGps();
            } else {
                this.avviaGps();
            }
        },

        avviaGps() {
            if (!navigator.geolocation) {
                this.statoGps = 'GPS non supportato dal browser';
                return;
            }
            this.attivo = true;
            this.statoGps = 'GPS attivo - posizione aggiornata ogni 60s';
            this.inviaPosizone();
            this.intervallo = setInterval(() => this.inviaPosizone(), 60000);
        },

        fermaGps() {
            this.attivo = false;
            this.statoGps = 'GPS non attivo';
            if (this.intervallo) clearInterval(this.intervallo);
        },

        inviaPosizone() {
            navigator.geolocation.getCurrentPosition(pos => {
                fetch('/tecnico/posizione', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitudine: pos.coords.latitude,
                        longitudine: pos.coords.longitude
                    })
                });
                this.statoGps = `Ultima posizione: ${new Date().toLocaleTimeString('it-IT')}`;
            });
        }
    }
}
</script>
@endpush
@endsection
