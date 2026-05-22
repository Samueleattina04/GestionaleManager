<?php
namespace App\Http\Controllers;

use App\Models\Articolo;
use App\Models\Giacenza;
use App\Models\Intervento;
use App\Models\Magazzino;
use App\Models\MovimentoMagazzino;
use App\Models\StatoTecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TecnicoController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $interventiOggi = Intervento::where('tecnico_id', $user->id)
            ->whereIn('stato', ['assegnato', 'in_corso'])
            ->with('cliente')
            ->orderBy('data_pianificata')
            ->get();

        $statoTecnico = StatoTecnico::firstOrCreate(
            ['user_id' => $user->id],
            ['tenant_id' => $user->tenant_id, 'disponibile' => false]
        );

        return view('tecnico.dashboard', compact('interventiOggi', 'statoTecnico'));
    }

    public function interventi(Request $request)
    {
        $user = Auth::user();
        $query = Intervento::where('tecnico_id', $user->id)->with('cliente')->orderBy('data_pianificata');
        if ($request->filled('stato')) $query->where('stato', $request->stato);
        if ($request->filled('data')) $query->whereDate('data_pianificata', $request->data);
        else $query->whereNotIn('stato', ['completato', 'annullato']);
        $interventi = $query->paginate(20);
        return view('tecnico.interventi', compact('interventi'));
    }

    public function dettaglioIntervento(Intervento $intervento)
    {
        abort_unless($intervento->tecnico_id === Auth::id(), 403);
        $intervento->load(['cliente', 'checklist', 'foto', 'articoli.articolo']);
        return view('tecnico.intervento', compact('intervento'));
    }

    public function magazzino(Request $request)
    {
        $user = Auth::user();
        $query = Articolo::where('tenant_id', $user->tenant_id)
            ->where('attivo', true)
            ->with(['giacenze' => fn($q) => $q->where('magazzino_id', function($sq) use ($user) {
                $sq->select('id')->from('magazzini')
                   ->where('tenant_id', $user->tenant_id)
                   ->where('principale', true)->limit(1);
            }), 'categoria']);
        if ($request->filled('ricerca')) {
            $q = $request->ricerca;
            $query->where(fn($sq) => $sq->where('codice', 'like', "%$q%")->orWhere('nome', 'like', "%$q%"));
        }
        $articoli = $query->orderBy('nome')->paginate(24);
        return view('tecnico.magazzino', compact('articoli'));
    }

    public function usaArticolo(Request $request)
    {
        $dati = $request->validate([
            'articolo_id' => 'required|exists:articoli,id',
            'quantita' => 'required|numeric|min:0.01',
            'intervento_id' => 'nullable|exists:interventi,id',
            'note' => 'nullable|string',
        ]);
        $user = Auth::user();
        $magazzino = Magazzino::where('tenant_id', $user->tenant_id)->where('principale', true)->firstOrFail();

        DB::transaction(function () use ($dati, $user, $magazzino) {
            Giacenza::where('articolo_id', $dati['articolo_id'])
                ->where('magazzino_id', $magazzino->id)
                ->decrement('quantita', $dati['quantita']);

            MovimentoMagazzino::create([
                'tenant_id' => $user->tenant_id,
                'articolo_id' => $dati['articolo_id'],
                'magazzino_origine_id' => $magazzino->id,
                'tipo' => 'scarico',
                'quantita' => $dati['quantita'],
                'riferimento' => $dati['intervento_id'] ? 'INT-'.$dati['intervento_id'] : null,
                'note' => $dati['note'] ?? null,
                'registrato_da' => $user->id,
            ]);
        });

        return redirect()->back()->with('successo', 'Utilizzo registrato correttamente.');
    }

    public function toggleDisponibilita(Request $request)
    {
        $user = Auth::user();
        $stato = StatoTecnico::firstOrCreate(
            ['user_id' => $user->id],
            ['tenant_id' => $user->tenant_id, 'disponibile' => false]
        );

        $nuovoStato = !$stato->disponibile;
        $stato->update([
            'disponibile' => $nuovoStato,
            'inizio_turno' => $nuovoStato ? now() : $stato->inizio_turno,
            'fine_turno' => !$nuovoStato ? now() : null,
        ]);

        return response()->json(['disponibile' => $nuovoStato]);
    }

    public function aggiornaPosizone(Request $request)
    {
        $dati = $request->validate([
            'latitudine' => 'required|numeric',
            'longitudine' => 'required|numeric',
        ]);

        $user = Auth::user();
        StatoTecnico::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tenant_id' => $user->tenant_id,
                'ultima_lat' => $dati['latitudine'],
                'ultima_lng' => $dati['longitudine'],
                'ultima_posizione_il' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }
}
