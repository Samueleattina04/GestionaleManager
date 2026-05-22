<?php
namespace App\Http\Controllers;

use App\Models\Preventivo;
use App\Models\PreventivoRiga;
use App\Models\Cliente;
use App\Models\Articolo;
use App\Models\Fattura;
use App\Models\FatturaRiga;
use App\Models\Intervento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventivoController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Preventivo::where('tenant_id', $this->tenantId())->with('cliente');
        if ($request->filled('stato')) $query->where('stato', $request->stato);
        $preventivi = $query->latest()->paginate(20)->withQueryString();
        return view('preventivi.index', compact('preventivi'));
    }

    public function create()
    {
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        $articoli = Articolo::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('descrizione')->get();
        return view('preventivi.create', compact('clienti', 'articoli'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clienti,id',
            'data' => 'required|date',
            'data_scadenza' => 'nullable|date|after:data',
            'oggetto' => 'nullable|string',
            'righe' => 'required|array|min:1',
            'righe.*.descrizione' => 'required|string',
            'righe.*.quantita' => 'required|numeric|min:0.001',
            'righe.*.prezzo_unitario' => 'required|numeric|min:0',
            'righe.*.iva' => 'required|numeric',
        ], [
            'cliente_id.required' => 'Il cliente è obbligatorio.',
            'righe.required' => 'Aggiungi almeno una riga.',
        ]);

        $anno = now()->year;
        $ultimo = Preventivo::where('tenant_id', $this->tenantId())->whereYear('data', $anno)->max('numero');
        $numero = $ultimo ? (intval(substr($ultimo, -4)) + 1) : 1;
        $numStr = 'PRV-' . $anno . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $numStr) {
            $totImponibile = 0;
            $totIva = 0;

            $preventivo = Preventivo::create([
                'tenant_id' => $this->tenantId(),
                'numero' => $numStr,
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'data' => $request->data,
                'data_scadenza' => $request->data_scadenza,
                'oggetto' => $request->oggetto,
                'note' => $request->note,
                'stato' => 'bozza',
                'totale_imponibile' => 0,
                'totale_iva' => 0,
                'totale' => 0,
            ]);

            foreach ($request->righe as $i => $riga) {
                $imp = $riga['quantita'] * $riga['prezzo_unitario'] * (1 - ($riga['sconto'] ?? 0) / 100);
                $iva = $imp * ($riga['iva'] / 100);
                PreventivoRiga::create([
                    'tenant_id' => $this->tenantId(),
                    'preventivo_id' => $preventivo->id,
                    'articolo_id' => $riga['articolo_id'] ?? null,
                    'descrizione' => $riga['descrizione'],
                    'unita_misura' => $riga['unita_misura'] ?? 'pz',
                    'quantita' => $riga['quantita'],
                    'prezzo_unitario' => $riga['prezzo_unitario'],
                    'sconto' => $riga['sconto'] ?? 0,
                    'iva' => $riga['iva'],
                    'totale' => $imp + $iva,
                    'ordine' => $i,
                ]);
                $totImponibile += $imp;
                $totIva += $iva;
            }

            $preventivo->update([
                'totale_imponibile' => $totImponibile,
                'totale_iva' => $totIva,
                'totale' => $totImponibile + $totIva,
            ]);
        });

        return redirect()->route('preventivi.index')->with('successo', 'Preventivo creato con successo.');
    }

    public function show(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $preventivo->load(['cliente', 'righe.articolo', 'utente']);
        return view('preventivi.show', compact('preventivo'));
    }

    public function edit(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        $articoli = Articolo::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('descrizione')->get();
        $preventivo->load('righe');
        return view('preventivi.edit', compact('preventivo', 'clienti', 'articoli'));
    }

    public function update(Request $request, Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $preventivo->update($request->only(['stato', 'note', 'oggetto', 'data_scadenza']));
        return redirect()->route('preventivi.show', $preventivo)->with('successo', 'Preventivo aggiornato.');
    }

    public function destroy(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $preventivo->delete();
        return redirect()->route('preventivi.index')->with('successo', 'Preventivo eliminato.');
    }

    public function invia(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $preventivo->update(['stato' => 'inviato', 'inviato_il' => now()]);
        return back()->with('successo', 'Preventivo segnato come inviato.');
    }

    public function convertiIntervento(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $intervento = Intervento::create([
            'tenant_id' => $this->tenantId(),
            'cliente_id' => $preventivo->cliente_id,
            'titolo' => 'Da preventivo ' . $preventivo->numero,
            'descrizione' => $preventivo->oggetto,
            'priorita' => 'normale',
            'stato' => 'da_assegnare',
            'numero' => 'INT-' . now()->year . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
        ]);
        $preventivo->update(['stato' => 'accettato']);
        return redirect()->route('interventi.show', $intervento)->with('successo', 'Intervento creato dal preventivo.');
    }

    public function convertiFattura(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $anno = now()->year;
        $num = Fattura::where('tenant_id', $this->tenantId())->where('anno', $anno)->max('numero');
        $numStr = $anno . '-' . str_pad(($num ? intval(substr($num, -4)) + 1 : 1), 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($preventivo, $numStr, $anno) {
            $fattura = Fattura::create([
                'tenant_id' => $this->tenantId(),
                'numero' => $numStr,
                'anno' => $anno,
                'tipo' => 'fattura',
                'cliente_id' => $preventivo->cliente_id,
                'preventivo_id' => $preventivo->id,
                'data' => now()->format('Y-m-d'),
                'data_scadenza' => now()->addDays(30)->format('Y-m-d'),
                'stato' => 'bozza',
                'totale_imponibile' => $preventivo->totale_imponibile,
                'totale_iva' => $preventivo->totale_iva,
                'totale' => $preventivo->totale,
            ]);

            foreach ($preventivo->righe as $i => $riga) {
                FatturaRiga::create([
                    'tenant_id' => $this->tenantId(),
                    'fattura_id' => $fattura->id,
                    'articolo_id' => $riga->articolo_id,
                    'descrizione' => $riga->descrizione,
                    'unita_misura' => $riga->unita_misura,
                    'quantita' => $riga->quantita,
                    'prezzo_unitario' => $riga->prezzo_unitario,
                    'sconto' => $riga->sconto,
                    'iva' => $riga->iva,
                    'totale' => $riga->totale,
                    'ordine' => $i,
                ]);
            }

            $preventivo->update(['stato' => 'accettato']);
        });

        return redirect()->route('fatture.index')->with('successo', 'Fattura creata dal preventivo.');
    }

    public function pdf(Preventivo $preventivo)
    {
        abort_unless($preventivo->tenant_id === $this->tenantId(), 403);
        $preventivo->load(['cliente', 'righe', 'utente']);
        $tenant = Auth::user()->tenant;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.preventivo', compact('preventivo', 'tenant'));
        return $pdf->download('preventivo-' . $preventivo->numero . '.pdf');
    }
}
