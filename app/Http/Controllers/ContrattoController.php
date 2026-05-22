<?php
namespace App\Http\Controllers;

use App\Models\Contratto;
use App\Models\ManutenzoneProgrammata;
use App\Models\Cliente;
use App\Models\Intervento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContrattoController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Contratto::where('tenant_id', $this->tenantId())->with('cliente');
        if ($request->filled('stato')) $query->where('stato', $request->stato);
        $contratti = $query->orderBy('data_fine')->paginate(20)->withQueryString();
        return view('contratti.index', compact('contratti'));
    }

    public function create()
    {
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        return view('contratti.create', compact('clienti'));
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'cliente_id' => 'required|exists:clienti,id',
            'titolo' => 'required|string|max:255',
            'tipo' => 'required|in:assistenza,manutenzione,noleggio,altro',
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after:data_inizio',
            'importo' => 'required|numeric|min:0',
            'frequenza_fatturazione' => 'required|in:mensile,trimestrale,semestrale,annuale',
            'frequenza_manutenzione' => 'nullable|in:settimanale,mensile,bimestrale,trimestrale,semestrale,annuale',
            'descrizione' => 'nullable|string',
            'note' => 'nullable|string',
            'rinnovo_automatico' => 'boolean',
        ]);

        $dati['tenant_id'] = $this->tenantId();
        $dati['stato'] = 'attivo';

        $ultimo = Contratto::where('tenant_id', $this->tenantId())->max('numero');
        $num = $ultimo ? (intval(substr($ultimo, 4)) + 1) : 1;
        $dati['numero'] = 'CTR-' . str_pad($num, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($dati, $request) {
            $contratto = Contratto::create($dati);

            if ($dati['frequenza_manutenzione'] ?? null) {
                $this->generaManutenzioni($contratto);
            }
        });

        return redirect()->route('contratti.index')->with('successo', 'Contratto creato con successo.');
    }

    public function show(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $contratto->load(['cliente', 'manutenzioni', 'interventi.tecnico']);
        return view('contratti.show', compact('contratto'));
    }

    public function edit(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $clienti = Cliente::where('tenant_id', $this->tenantId())->orderBy('ragione_sociale')->get();
        return view('contratti.edit', compact('contratto', 'clienti'));
    }

    public function update(Request $request, Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $contratto->update($request->only(['titolo', 'stato', 'importo', 'note', 'descrizione', 'data_fine']));
        return redirect()->route('contratti.show', $contratto)->with('successo', 'Contratto aggiornato.');
    }

    public function destroy(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $contratto->delete();
        return redirect()->route('contratti.index')->with('successo', 'Contratto eliminato.');
    }

    public function rinnova(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);

        $durata = $contratto->data_inizio->diffInDays($contratto->data_fine);
        $nuovaInizio = $contratto->data_fine->addDay();
        $nuovaFine = $nuovaInizio->copy()->addDays($durata);

        $nuovoContratto = $contratto->replicate();
        $nuovoContratto->data_inizio = $nuovaInizio;
        $nuovoContratto->data_fine = $nuovaFine;
        $nuovoContratto->stato = 'attivo';
        $num = Contratto::where('tenant_id', $this->tenantId())->max('numero');
        $nuovoContratto->numero = 'CTR-' . str_pad((intval(substr($num, 4)) + 1), 4, '0', STR_PAD_LEFT);
        $nuovoContratto->save();

        $contratto->update(['stato' => 'scaduto']);

        return redirect()->route('contratti.show', $nuovoContratto)->with('successo', 'Contratto rinnovato con successo.');
    }

    public function manutenzioni(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $manutenzioni = $contratto->manutenzioni()->with('intervento')->orderBy('data_pianificata')->paginate(20);
        return view('contratti.manutenzioni', compact('contratto', 'manutenzioni'));
    }

    public function generaManutenzioniPubbliche(Contratto $contratto)
    {
        abort_unless($contratto->tenant_id === $this->tenantId(), 403);
        $contratto->manutenzioni()->delete();
        $this->generaManutenzioni($contratto);
        return redirect()->route('contratti.manutenzioni', $contratto)->with('successo', 'Manutenzioni generate con successo.');
    }

    private function generaManutenzioni(Contratto $contratto): void
    {
        $intervallo = match($contratto->frequenza_manutenzione) {
            'settimanale' => '1 week',
            'mensile' => '1 month',
            'bimestrale' => '2 months',
            'trimestrale' => '3 months',
            'semestrale' => '6 months',
            'annuale' => '1 year',
            default => null,
        };

        if (!$intervallo) return;

        $data = Carbon::parse($contratto->data_inizio);
        $fine = Carbon::parse($contratto->data_fine);

        while ($data->lte($fine)) {
            ManutenzoneProgrammata::create([
                'tenant_id' => $contratto->tenant_id,
                'contratto_id' => $contratto->id,
                'cliente_id' => $contratto->cliente_id,
                'data_pianificata' => $data->format('Y-m-d'),
                'stato' => 'programmata',
            ]);
            $data->add($intervallo);
        }
    }
}
