@extends('layouts.app')
@section('titolo', 'Report')

@section('contenuto')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Centro Report</h5>
        <p class="text-muted mb-0">Analisi e statistiche dell'azienda</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('report.fatturato') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:72px;height:72px;">
                            <i class="bi bi-bar-chart-line text-primary" style="font-size:2rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Report Fatturato</h6>
                    <p class="text-muted mb-0 small">Andamento fatturato e incassi per mese e anno</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                    <span class="text-primary small fw-semibold">Visualizza <i class="bi bi-arrow-right"></i></span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <a href="{{ route('report.interventi') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10" style="width:72px;height:72px;">
                            <i class="bi bi-tools text-warning" style="font-size:2rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Report Interventi</h6>
                    <p class="text-muted mb-0 small">Statistiche interventi per tecnico, stato e periodo</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                    <span class="text-warning small fw-semibold">Visualizza <i class="bi bi-arrow-right"></i></span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <a href="{{ route('report.magazzino') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:72px;height:72px;">
                            <i class="bi bi-box-seam text-success" style="font-size:2rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Report Magazzino</h6>
                    <p class="text-muted mb-0 small">Valore scorte, articoli critici e movimenti</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                    <span class="text-success small fw-semibold">Visualizza <i class="bi bi-arrow-right"></i></span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''" data-bs-toggle="modal" data-bs-target="#modalExport">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10" style="width:72px;height:72px;">
                        <i class="bi bi-download text-info" style="font-size:2rem;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-1">Export Generale</h6>
                <p class="text-muted mb-0 small">Esporta tutti i dati in formato Excel o PDF</p>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                <span class="text-info small fw-semibold">Esporta <i class="bi bi-arrow-right"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Accesso Rapido ai Report</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <a href="{{ route('report.fatturato', ['anno' => now()->year]) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-calendar-check me-2"></i>Fatturato {{ now()->year }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <a href="{{ route('report.fatturato', ['anno' => now()->year - 1]) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-calendar me-2"></i>Fatturato {{ now()->year - 1 }}
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <a href="{{ route('report.interventi', ['data_da' => now()->startOfMonth()->format('Y-m-d'), 'data_a' => now()->format('Y-m-d')]) }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-tools me-2"></i>Interventi del mese
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <a href="{{ route('report.magazzino') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-exclamation-triangle me-2"></i>Articoli sotto scorta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExport" tabindex="-1" aria-labelledby="modalExportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalExportLabel">
                    <i class="bi bi-download me-2"></i>Export Generale
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Seleziona il periodo e il formato di esportazione desiderato.</p>
                <form action="#" method="GET" id="formExport">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-medium">Data inizio</label>
                            <input type="date" name="data_da" class="form-control" value="{{ now()->startOfYear()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">Data fine</label>
                            <input type="date" name="data_a" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Formato</label>
                            <select name="formato" class="form-select">
                                <option value="xlsx">Excel (.xlsx)</option>
                                <option value="pdf">PDF (.pdf)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Sezioni da includere</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sezioni[]" value="fatturato" id="chkFatturato" checked>
                                <label class="form-check-label" for="chkFatturato">Fatturato e incassi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sezioni[]" value="interventi" id="chkInterventi" checked>
                                <label class="form-check-label" for="chkInterventi">Interventi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sezioni[]" value="magazzino" id="chkMagazzino" checked>
                                <label class="form-check-label" for="chkMagazzino">Magazzino</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="submit" form="formExport" class="btn btn-primary">
                    <i class="bi bi-download me-1"></i>Esporta
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
