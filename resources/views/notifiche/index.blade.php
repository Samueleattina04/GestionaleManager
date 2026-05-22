@extends('layouts.app')
@section('titolo', 'Notifiche')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Notifiche</h5>
    <form action="{{ route('notifiche.leggi-tutte') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-check-all me-1"></i>Segna tutte come lette
        </button>
    </form>
</div>

<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifiche as $notifica)
        <div class="list-group-item {{ !$notifica->letta ? 'bg-blue-50' : '' }}">
            <div class="d-flex align-items-start gap-3">
                @if(!$notifica->letta)
                <div class="bg-primary rounded-circle mt-2 flex-shrink-0" style="width:8px; height:8px;"></div>
                @else
                <div class="mt-2 flex-shrink-0" style="width:8px; height:8px;"></div>
                @endif
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $notifica->titolo }}</div>
                    <p class="text-muted mb-1" style="font-size:0.875rem">{{ $notifica->messaggio }}</p>
                    <small class="text-muted">{{ $notifica->created_at->diffForHumans() }}</small>
                </div>
                @if(!$notifica->letta)
                <form action="{{ route('notifiche.leggi', $notifica) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-check2"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center p-5 text-muted">
            <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
            Nessuna notifica
        </div>
        @endforelse
    </div>
    @if($notifiche->hasPages())
    <div class="card-footer">{{ $notifiche->links() }}</div>
    @endif
</div>
@endsection
