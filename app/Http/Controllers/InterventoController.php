<?php
namespace App\Http\Controllers;

use App\Models\Intervento;
use App\Models\Cliente;
use App\Models\User;
use App\Models\InterventoChecklist;
use App\Models\InterventoFoto;
use App\Models\TimerIntervento;
use App\Models\MovimentoMagazzino;
use App\Models\Giacenza;
use App\Models\Fattura;
use App\Models\FatturaRiga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InterventoController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Intervento::where('tenant_id', $this->tenantId())
            ->with(['cliente', 'tecnico']);

        if ($request->filled('stato')) $query->where('stato', $request->stato);
        if ($request->filled('priorita')) $query->where('priorita', $request->priorita);
        if ($request->filled('tecnico')) $query->where('tecnico_id', $request->tecnico);
        if ($request->filled('cerca')) {
            $query->where(function ($q) use ($request) {
                $q->where('titolo', 'like', '%'.$request->cerca.'%')
                  ->orWhereHas('cliente', fn($cq) => $cq->where('ragione_sociale', 'like', '%'.$request->cerca.'%'));
            });
        }

        $interventi = $query->orderByRaw("FIELD(stato, 'in_corso', 'da_assegnare', 'assegnato', 'completato', 'annullato')")
            ->orderBy('data_pianificata')
            ->paginate(20)->withQueryString();

        $tecnici = User::where('tenant_id', $this->tenantId())->role('tecnico')->get();

        return view('interventi.index', compact('interventi', 'tecnici'));
    }

    public function create()
    {
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        $tecnici = User::where('tenant_id', $this->tenantId())->role('tecnico')->get();
        return view('interventi.create', compact('clienti', 'tecnici'));
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'cliente_id' => 'required|exists:clienti,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'priorita' => 'required|in:urgente,normale,bassa',
            'tecnico_id' => 'nullable|exists:users,id',
            'data_pianificata' => 'nullable|date',
            'indirizzo_intervento' => 'nullable|string|max:255',
            'note_interne' => 'nullable|string',
        ], [
            'cliente_id.required' => 'Il cliente è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
        ]);

        $dati['tenant_id'] = $this->tenantId();
        $dati['stato'] = $dati['tecnico_id'] ? 'assegnato' : 'da_assegnare';

        // Numerazione automatica
        $ultimo = Intervento::where('tenant_id', $this->tenantId())
            ->whereYear('created_at', now()->year)
            ->max('numero');
        $numero = $ultimo ? (intval(substr($ultimo, -4)) + 1) : 1;
        $dati['numero'] = 'INT-' . now()->year . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        $intervento = Intervento::create($dati);

        // Checklist di default se configurata
        if ($request->filled('checklist')) {
            foreach ($request->checklist as $i => $voce) {
                if (trim($voce)) {
                    InterventoChecklist::create([
                        'tenant_id' => $this->tenantId(),
                        'intervento_id' => $intervento->id,
                        'voce' => $voce,
                        'ordine' => $i,
                    ]);
                }
            }
        }

        return redirect()->route('interventi.show', $intervento)
            ->with('successo', 'Intervento creato con successo.');
    }

    public function show(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $intervento->load(['cliente', 'tecnico', 'checklist', 'foto', 'articoli.articolo', 'timers.utente']);
        $tecnici = User::where('tenant_id', $this->tenantId())->role('tecnico')->get();
        return view('interventi.show', compact('intervento', 'tecnici'));
    }

    public function edit(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        $tecnici = User::where('tenant_id', $this->tenantId())->role('tecnico')->get();
        return view('interventi.edit', compact('intervento', 'clienti', 'tecnici'));
    }

    public function update(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $dati = $request->validate([
            'cliente_id' => 'required|exists:clienti,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'priorita' => 'required|in:urgente,normale,bassa',
            'tecnico_id' => 'nullable|exists:users,id',
            'data_pianificata' => 'nullable|date',
            'indirizzo_intervento' => 'nullable|string|max:255',
            'note_interne' => 'nullable|string',
            'note_cliente' => 'nullable|string',
        ]);
        $intervento->update($dati);
        return redirect()->route('interventi.show', $intervento)->with('successo', 'Intervento aggiornato.');
    }

    public function destroy(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $intervento->delete();
        return redirect()->route('interventi.index')->with('successo', 'Intervento eliminato.');
    }

    public function assegna(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $request->validate(['tecnico_id' => 'required|exists:users,id']);
        $intervento->update([
            'tecnico_id' => $request->tecnico_id,
            'stato' => 'assegnato',
        ]);
        return back()->with('successo', 'Tecnico assegnato con successo.');
    }

    public function cambiaStato(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $request->validate(['stato' => 'required|in:da_assegnare,assegnato,in_corso,completato,annullato']);

        $dati = ['stato' => $request->stato];
        if ($request->stato === 'in_corso' && !$intervento->data_inizio_effettivo) {
            $dati['data_inizio_effettivo'] = now();
        }
        if ($request->stato === 'completato' && !$intervento->data_fine_effettivo) {
            $dati['data_fine_effettivo'] = now();
        }

        $intervento->update($dati);
        return back()->with('successo', 'Stato aggiornato.');
    }

    public function caricaFoto(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $request->validate(['foto.*' => 'required|image|max:5120']);

        foreach ($request->file('foto', []) as $file) {
            $percorso = $file->store('interventi/' . $intervento->id, 'public');
            InterventoFoto::create([
                'tenant_id' => $this->tenantId(),
                'intervento_id' => $intervento->id,
                'user_id' => Auth::id(),
                'percorso' => $percorso,
                'nome_originale' => $file->getClientOriginalName(),
            ]);
        }

        return back()->with('successo', 'Foto caricate con successo.');
    }

    public function eliminaFoto(Intervento $intervento, InterventoFoto $foto)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        Storage::disk('public')->delete($foto->percorso);
        $foto->delete();
        return back()->with('successo', 'Foto eliminata.');
    }

    public function aggiungiChecklist(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $request->validate(['voce' => 'required|string|max:255']);

        $ordine = InterventoChecklist::where('intervento_id', $intervento->id)->max('ordine') + 1;
        InterventoChecklist::create([
            'tenant_id' => $this->tenantId(),
            'intervento_id' => $intervento->id,
            'voce' => $request->voce,
            'ordine' => $ordine,
        ]);

        return back()->with('successo', 'Voce aggiunta alla checklist.');
    }

    public function aggiornaChecklist(Request $request, Intervento $intervento, InterventoChecklist $item)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $item->update([
            'completata' => $request->boolean('completata'),
            'completata_il' => $request->boolean('completata') ? now() : null,
            'completata_da' => $request->boolean('completata') ? Auth::id() : null,
        ]);
        return response()->json(['ok' => true]);
    }

    public function startTimer(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        TimerIntervento::create([
            'tenant_id' => $this->tenantId(),
            'intervento_id' => $intervento->id,
            'user_id' => Auth::id(),
            'inizio' => now(),
        ]);
        $intervento->update(['stato' => 'in_corso', 'data_inizio_effettivo' => $intervento->data_inizio_effettivo ?? now()]);
        return back()->with('successo', 'Timer avviato.');
    }

    public function stopTimer(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $timer = TimerIntervento::where('intervento_id', $intervento->id)
            ->where('user_id', Auth::id())
            ->whereNull('fine')
            ->latest()
            ->first();

        if ($timer) {
            $minuti = now()->diffInMinutes($timer->inizio);
            $timer->update(['fine' => now(), 'minuti' => $minuti]);
            $intervento->increment('minuti_lavorati', $minuti);
        }

        return back()->with('successo', 'Timer fermato. Ore registrate: ' . intdiv($minuti ?? 0, 60) . 'h ' . (($minuti ?? 0) % 60) . 'min');
    }

    public function salvaFirma(Request $request, Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $request->validate(['firma' => 'required|string']);

        $firmaData = $request->firma;
        $firmaData = str_replace('data:image/png;base64,', '', $firmaData);
        $firmaData = base64_decode($firmaData);
        $nomefile = 'firme/firma_' . $intervento->id . '_' . time() . '.png';
        Storage::disk('public')->put($nomefile, $firmaData);

        $intervento->update(['firma_cliente' => $nomefile]);

        return back()->with('successo', 'Firma salvata con successo.');
    }

    public function convertiFattura(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);

        $anno = now()->year;
        $ultimoNum = Fattura::where('tenant_id', $this->tenantId())->where('anno', $anno)->where('tipo', 'fattura')->max('numero');
        $numero = $ultimoNum ? (intval(substr($ultimoNum, -4)) + 1) : 1;
        $numStr = now()->year . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($intervento, $numStr, $anno) {
            $fattura = Fattura::create([
                'tenant_id' => $this->tenantId(),
                'numero' => $numStr,
                'anno' => $anno,
                'tipo' => 'fattura',
                'cliente_id' => $intervento->cliente_id,
                'data' => now()->format('Y-m-d'),
                'data_scadenza' => now()->addDays(30)->format('Y-m-d'),
                'stato' => 'bozza',
            ]);

            $totale = 0;
            foreach ($intervento->articoli as $ia) {
                $imp = $ia->quantita * $ia->prezzo_unitario * (1 - $ia->sconto / 100);
                FatturaRiga::create([
                    'tenant_id' => $this->tenantId(),
                    'fattura_id' => $fattura->id,
                    'articolo_id' => $ia->articolo_id,
                    'descrizione' => $ia->articolo->descrizione,
                    'unita_misura' => $ia->articolo->unita_misura,
                    'quantita' => $ia->quantita,
                    'prezzo_unitario' => $ia->prezzo_unitario,
                    'sconto' => $ia->sconto,
                    'iva' => $ia->articolo->iva,
                    'totale' => $imp,
                    'ordine' => 0,
                ]);
                $totale += $imp;
            }

            $iva = $totale * 0.22;
            $fattura->update([
                'totale_imponibile' => $totale,
                'totale_iva' => $iva,
                'totale' => $totale + $iva,
            ]);

            $intervento->update(['fatturato' => true, 'fattura_id' => $fattura->id]);
        });

        return redirect()->route('fatture.index')->with('successo', 'Fattura creata dall\'intervento.');
    }

    public function pdf(Intervento $intervento)
    {
        abort_unless($intervento->tenant_id === $this->tenantId(), 403);
        $intervento->load(['cliente', 'tecnico', 'checklist', 'articoli.articolo']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.intervento', compact('intervento'));
        return $pdf->download('intervento-' . $intervento->numero . '.pdf');
    }
}
