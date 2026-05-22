<?php
namespace App\Http\Controllers;

use App\Models\Magazzino;
use App\Models\Articolo;
use App\Models\Giacenza;
use App\Models\MovimentoMagazzino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MagazzinoController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index()
    {
        $magazzini = Magazzino::where('tenant_id', $this->tenantId())
            ->with(['responsabile', 'giacenze'])
            ->get();
        return view('magazzini.index', compact('magazzini'));
    }

    public function create()
    {
        $responsabili = \App\Models\User::where('tenant_id', $this->tenantId())->get();
        return view('magazzini.create', compact('responsabili'));
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'principale' => 'boolean',
            'mobile' => 'boolean',
            'responsabile_id' => 'nullable|exists:users,id',
        ]);
        $dati['tenant_id'] = $this->tenantId();

        if ($request->boolean('principale')) {
            Magazzino::where('tenant_id', $this->tenantId())->update(['principale' => false]);
        }

        Magazzino::create($dati);
        return redirect()->route('magazzini.index')->with('successo', 'Magazzino creato con successo.');
    }

    public function show(Magazzino $magazzino)
    {
        abort_unless($magazzino->tenant_id === $this->tenantId(), 403);
        $magazzino->load(['responsabile', 'giacenze.articolo.categoria']);
        return view('magazzini.show', compact('magazzino'));
    }

    public function edit(Magazzino $magazzino)
    {
        abort_unless($magazzino->tenant_id === $this->tenantId(), 403);
        $responsabili = \App\Models\User::where('tenant_id', $this->tenantId())->get();
        return view('magazzini.edit', compact('magazzino', 'responsabili'));
    }

    public function update(Request $request, Magazzino $magazzino)
    {
        abort_unless($magazzino->tenant_id === $this->tenantId(), 403);
        $dati = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'responsabile_id' => 'nullable|exists:users,id',
        ]);
        $magazzino->update($dati);
        return redirect()->route('magazzini.show', $magazzino)->with('successo', 'Magazzino aggiornato.');
    }

    public function destroy(Magazzino $magazzino)
    {
        abort_unless($magazzino->tenant_id === $this->tenantId(), 403);
        if ($magazzino->principale) {
            return back()->with('errore', 'Non puoi eliminare il magazzino principale.');
        }
        $magazzino->delete();
        return redirect()->route('magazzini.index')->with('successo', 'Magazzino eliminato.');
    }

    public function registraMovimento(Request $request)
    {
        $dati = $request->validate([
            'articolo_id' => 'required|exists:articoli,id',
            'magazzino_id' => 'required|exists:magazzini,id',
            'magazzino_destinazione_id' => 'nullable|exists:magazzini,id',
            'tipo' => 'required|in:carico,scarico,trasferimento,inventario',
            'quantita' => 'required|numeric|min:0.001',
            'prezzo_unitario' => 'nullable|numeric|min:0',
            'causale' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $tenantId = $this->tenantId();
        $dati['tenant_id'] = $tenantId;
        $dati['user_id'] = Auth::id();

        DB::transaction(function () use ($dati, $tenantId) {
            MovimentoMagazzino::create($dati);

            $segno = in_array($dati['tipo'], ['carico', 'inventario']) ? 1 : -1;
            if ($dati['tipo'] === 'inventario') $segno = 0;

            $giacenza = Giacenza::firstOrCreate(
                ['articolo_id' => $dati['articolo_id'], 'magazzino_id' => $dati['magazzino_id']],
                ['tenant_id' => $tenantId, 'quantita' => 0]
            );

            if ($dati['tipo'] === 'inventario') {
                $giacenza->update(['quantita' => $dati['quantita']]);
            } elseif ($dati['tipo'] === 'trasferimento') {
                $giacenza->decrement('quantita', $dati['quantita']);
                $dest = Giacenza::firstOrCreate(
                    ['articolo_id' => $dati['articolo_id'], 'magazzino_id' => $dati['magazzino_destinazione_id']],
                    ['tenant_id' => $tenantId, 'quantita' => 0]
                );
                $dest->increment('quantita', $dati['quantita']);
            } else {
                $giacenza->increment('quantita', $dati['quantita'] * $segno);
            }
        });

        return back()->with('successo', 'Movimento registrato con successo.');
    }

    public function inventario()
    {
        $articoli = Articolo::where('tenant_id', $this->tenantId())
            ->with(['giacenze.magazzino', 'categoria'])
            ->orderBy('descrizione')
            ->get();
        $magazzini = Magazzino::where('tenant_id', $this->tenantId())->get();
        return view('magazzini.inventario', compact('articoli', 'magazzini'));
    }

    public function articoliSottoScorta()
    {
        $articoli = Articolo::where('tenant_id', $this->tenantId())
            ->with(['giacenze.magazzino', 'categoria'])
            ->get()
            ->filter(fn($a) => $a->isSottoScortaMinima());
        return view('magazzini.sotto-scorta', compact('articoli'));
    }
}
