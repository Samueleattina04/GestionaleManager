@extends('layouts.app')
@section('titolo', 'Intervento ' . $intervento->numero)

@section('contenuto')
<div class="mb-3">
    <a href="{{ route('tecnico.dashboard') }}" class="text-muted text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Torna alla lista
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Info intervento -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ $intervento->titolo }}</span>
                <div class="d-flex gap-2">
                    <span class="badge badge-priorita-{{ $intervento->priorita }}">{{ $intervento->priorita_label }}</span>
                    <span class="badge badge-stato-{{ $intervento->stato }}">{{ $intervento->stato_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Cliente</label>
                        <p class="fw-semibold mb-1">{{ $intervento->cliente->ragione_sociale }}</p>
                        @if($intervento->cliente->telefono)
                        <a href="tel:{{ $intervento->cliente->telefono }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-telephone me-1"></i>{{ $intervento->cliente->telefono }}
                        </a>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Indirizzo</label>
                        @php $addr = $intervento->indirizzo_intervento ?? $intervento->cliente->indirizzo_completo; @endphp
                        <p class="mb-2">{{ $addr }}</p>
                        @if($addr)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($addr) }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="bi bi-map me-1"></i>Apri in Google Maps
                        </a>
                        @endif
                    </div>
                    @if($intervento->descrizione)
                    <div class="col-12">
                        <label class="form-label text-muted small fw-semibold text-uppercase">Descrizione</label>
                        <p class="mb-0">{{ $intervento->descrizione }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timer lavoro -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-stopwatch me-2"></i>Timer Lavoro
            </div>
            <div class="card-body" x-data="timer({{ $intervento->minuti_lavorati }})">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2 fw-bold font-monospace" x-text="formatTime()">
                        {{ intdiv($intervento->minuti_lavorati, 60) }}:{{ str_pad($intervento->minuti_lavorati % 60, 2, '0', STR_PAD_LEFT) }}:00
                    </div>
                    @if($intervento->stato !== 'completato')
                    <form action="{{ route('interventi.timer.start', $intervento) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-play-fill me-1"></i>Start
                        </button>
                    </form>
                    <form action="{{ route('interventi.timer.stop', $intervento) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-stop-fill me-1"></i>Stop
                        </button>
                    </form>
                    @endif
                </div>
                <small class="text-muted d-block mt-2">
                    Ore registrate: {{ intdiv($intervento->minuti_lavorati, 60) }}h {{ $intervento->minuti_lavorati % 60 }}min
                </small>
            </div>
        </div>

        <!-- Checklist -->
        @if($intervento->checklist->count() > 0)
        <div class="card mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-check2-square me-2"></i>Checklist Attività
                <span class="badge bg-secondary ms-2">
                    {{ $intervento->checklist->where('completata', true)->count() }}/{{ $intervento->checklist->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                @foreach($intervento->checklist as $item)
                <div class="d-flex align-items-center p-3 border-bottom"
                     x-data="{ completata: {{ $item->completata ? 'true' : 'false' }} }">
                    <div class="form-check flex-grow-1">
                        <input class="form-check-input" type="checkbox"
                               :checked="completata"
                               @change="fetch('{{ route('interventi.checklist.aggiorna', [$intervento, $item]) }}', {method:'PATCH', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body:JSON.stringify({completata:!completata})}).then(()=>completata=!completata)"
                               id="check_{{ $item->id }}">
                        <label class="form-check-label" for="check_{{ $item->id }}"
                               :class="completata ? 'text-decoration-line-through text-muted' : ''">
                            {{ $item->voce }}
                        </label>
                    </div>
                    <small class="text-muted" x-show="completata">
                        {{ $item->completata_il?->format('H:i') ?? '' }}
                    </small>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Carica foto -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-camera me-2"></i>Foto
                <span class="badge bg-secondary ms-2">{{ $intervento->foto->count() }}</span>
            </div>
            <div class="card-body">
                @if($intervento->foto->count() > 0)
                <div class="row g-2 mb-3">
                    @foreach($intervento->foto as $foto)
                    <div class="col-4 col-sm-3 col-md-2">
                        <div class="position-relative">
                            <a href="{{ $foto->url }}" target="_blank">
                                <img src="{{ $foto->url }}" class="img-fluid rounded" style="aspect-ratio:1; object-fit:cover;">
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <form action="{{ route('interventi.foto', $intervento) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex gap-2">
                        <input type="file" name="foto[]" class="form-control form-control-sm" accept="image/*" capture="environment" multiple>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-upload me-1"></i>Carica
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Cambia stato -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">Aggiorna Stato</div>
            <div class="card-body">
                <form action="{{ route('interventi.cambia-stato', $intervento) }}" method="POST">
                    @csrf
                    <select name="stato" class="form-select mb-2">
                        @foreach(['in_corso' => 'In corso', 'completato' => 'Completato'] as $v => $l)
                        <option value="{{ $v }}" {{ $intervento->stato === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Aggiorna Stato</button>
                </form>
            </div>
        </div>

        <!-- Firma cliente -->
        <div class="card mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-pen me-2"></i>Firma Cliente
            </div>
            <div class="card-body">
                @if($intervento->firma_cliente)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $intervento->firma_cliente) }}" class="img-fluid border rounded" style="max-height:80px;">
                        <small class="text-success d-block mt-1"><i class="bi bi-check-circle me-1"></i>Firma acquisita</small>
                    </div>
                @endif
                <div x-data="{ disegno: null }" class="mb-2">
                    <canvas id="firmaCanvas" width="300" height="120" style="border:1px solid #d1d5db; border-radius:4px; touch-action:none; background:white;"></canvas>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" onclick="cancellaFirma()" class="btn btn-sm btn-outline-secondary flex-grow-1">
                            <i class="bi bi-eraser me-1"></i>Cancella
                        </button>
                        <button type="button" onclick="salvaFirma()" class="btn btn-sm btn-success flex-grow-1">
                            <i class="bi bi-check2 me-1"></i>Salva
                        </button>
                    </div>
                    <form id="firmaForm" action="{{ route('interventi.firma', $intervento) }}" method="POST">
                        @csrf
                        <input type="hidden" name="firma" id="firmaInput">
                    </form>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="card">
            <div class="card-header fw-semibold">Note</div>
            <div class="card-body">
                <form action="{{ route('interventi.update', $intervento) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="cliente_id" value="{{ $intervento->cliente_id }}">
                    <input type="hidden" name="titolo" value="{{ $intervento->titolo }}">
                    <input type="hidden" name="priorita" value="{{ $intervento->priorita }}">
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Note per il cliente</label>
                        <textarea name="note_cliente" class="form-control form-control-sm" rows="3">{{ $intervento->note_cliente }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Salva Note</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Canvas firma
const canvas = document.getElementById('firmaCanvas');
const ctx = canvas.getContext('2d');
let isDrawing = false;
let lastX = 0, lastY = 0;

function getPos(e) {
    const r = canvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return [src.clientX - r.left, src.clientY - r.top];
}

canvas.addEventListener('mousedown', e => { isDrawing = true; [lastX, lastY] = getPos(e); });
canvas.addEventListener('mousemove', e => {
    if (!isDrawing) return;
    const [x, y] = getPos(e);
    ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y);
    ctx.strokeStyle = '#1a1a1a'; ctx.lineWidth = 2; ctx.stroke();
    [lastX, lastY] = [x, y];
});
canvas.addEventListener('mouseup', () => isDrawing = false);
canvas.addEventListener('touchstart', e => { e.preventDefault(); isDrawing = true; [lastX, lastY] = getPos(e); }, {passive:false});
canvas.addEventListener('touchmove', e => {
    e.preventDefault();
    if (!isDrawing) return;
    const [x, y] = getPos(e);
    ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y);
    ctx.strokeStyle = '#1a1a1a'; ctx.lineWidth = 2; ctx.stroke();
    [lastX, lastY] = [x, y];
}, {passive:false});
canvas.addEventListener('touchend', () => isDrawing = false);

function cancellaFirma() { ctx.clearRect(0, 0, canvas.width, canvas.height); }
function salvaFirma() {
    document.getElementById('firmaInput').value = canvas.toDataURL('image/png');
    document.getElementById('firmaForm').submit();
}

// Timer
function timer(minuiIniziali) {
    return {
        secondi: minuiIniziali * 60,
        formatTime() {
            const h = Math.floor(this.secondi / 3600);
            const m = Math.floor((this.secondi % 3600) / 60);
            const s = this.secondi % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }
    }
}
</script>
@endpush
@endsection
