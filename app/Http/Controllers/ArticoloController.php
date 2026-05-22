<?php
namespace App\Http\Controllers;

use App\Models\Articolo;
use App\Models\CategoriaArticolo;
use App\Models\MovimentoMagazzino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticoloController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Articolo::where('tenant_id', $this->tenantId())
            ->with(['categoria', 'giacenze.magazzino']);

        if ($request->filled('cerca')) {
            $query->where(function ($q) use ($request) {
                $q->where('descrizione', 'like', '%'.$request->cerca.'%')
                  ->orWhere('codice', 'like', '%'.$request->cerca.'%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->boolean('sotto_scorta')) {
            $query->whereHas('giacenze', function ($q) {
                $q->whereRaw('quantita <= (SELECT scorta_minima FROM articoli WHERE articoli.id = giacenze.articolo_id)');
            });
        }

        $articoli = $query->orderBy('descrizione')->paginate(20)->withQueryString();
        $categorie = CategoriaArticolo::where('tenant_id', $this->tenantId())->get();

        return view('articoli.index', compact('articoli', 'categorie'));
    }

    public function create()
    {
        $categorie = CategoriaArticolo::where('tenant_id', $this->tenantId())->get();
        return view('articoli.create', compact('categorie'));
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'codice' => 'nullable|string|max:50',
            'descrizione' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorie_articoli,id',
            'unita_misura' => 'required|string|max:20',
            'prezzo_acquisto' => 'required|numeric|min:0',
            'prezzo_vendita' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0|max:100',
            'scorta_minima' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ], [
            'descrizione.required' => 'La descrizione è obbligatoria.',
            'prezzo_acquisto.required' => 'Il prezzo di acquisto è obbligatorio.',
            'prezzo_vendita.required' => 'Il prezzo di vendita è obbligatorio.',
        ]);

        $dati['tenant_id'] = $this->tenantId();
        Articolo::create($dati);

        return redirect()->route('articoli.index')
            ->with('successo', 'Articolo creato con successo.');
    }

    public function show(Articolo $articolo)
    {
        abort_unless($articolo->tenant_id === $this->tenantId(), 403);
        $articolo->load(['categoria', 'giacenze.magazzino']);
        return view('articoli.show', compact('articolo'));
    }

    public function edit(Articolo $articolo)
    {
        abort_unless($articolo->tenant_id === $this->tenantId(), 403);
        $categorie = CategoriaArticolo::where('tenant_id', $this->tenantId())->get();
        return view('articoli.edit', compact('articolo', 'categorie'));
    }

    public function update(Request $request, Articolo $articolo)
    {
        abort_unless($articolo->tenant_id === $this->tenantId(), 403);
        $dati = $request->validate([
            'codice' => 'nullable|string|max:50',
            'descrizione' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorie_articoli,id',
            'unita_misura' => 'required|string|max:20',
            'prezzo_acquisto' => 'required|numeric|min:0',
            'prezzo_vendita' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0|max:100',
            'scorta_minima' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);
        $articolo->update($dati);
        return redirect()->route('articoli.show', $articolo)->with('successo', 'Articolo aggiornato.');
    }

    public function destroy(Articolo $articolo)
    {
        abort_unless($articolo->tenant_id === $this->tenantId(), 403);
        $articolo->delete();
        return redirect()->route('articoli.index')->with('successo', 'Articolo eliminato.');
    }

    public function movimenti(Articolo $articolo)
    {
        abort_unless($articolo->tenant_id === $this->tenantId(), 403);
        $movimenti = MovimentoMagazzino::where('articolo_id', $articolo->id)
            ->with(['magazzino', 'destinazione', 'utente'])
            ->latest()
            ->paginate(20);
        return view('articoli.movimenti', compact('articolo', 'movimenti'));
    }
}
