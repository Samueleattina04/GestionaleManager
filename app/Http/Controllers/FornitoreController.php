<?php
namespace App\Http\Controllers;

use App\Models\Fornitore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FornitoreController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Fornitore::where('tenant_id', $this->tenantId());
        if ($request->filled('cerca')) {
            $query->where('ragione_sociale', 'like', '%'.$request->cerca.'%')
                  ->orWhere('email', 'like', '%'.$request->cerca.'%');
        }
        $fornitori = $query->orderBy('ragione_sociale')->paginate(20)->withQueryString();
        return view('fornitori.index', compact('fornitori'));
    }

    public function create() { return view('fornitori.create'); }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'partita_iva' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:5',
            'cap' => 'nullable|string|max:10',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pec' => 'nullable|email|max:255',
            'codice_sdi' => 'nullable|string|max:10',
            'note' => 'nullable|string',
        ]);
        $dati['tenant_id'] = $this->tenantId();
        Fornitore::create($dati);
        return redirect()->route('fornitori.index')->with('successo', 'Fornitore creato con successo.');
    }

    public function show(Fornitore $fornitore)
    {
        abort_unless($fornitore->tenant_id === $this->tenantId(), 403);
        return view('fornitori.show', compact('fornitore'));
    }

    public function edit(Fornitore $fornitore)
    {
        abort_unless($fornitore->tenant_id === $this->tenantId(), 403);
        return view('fornitori.edit', compact('fornitore'));
    }

    public function update(Request $request, Fornitore $fornitore)
    {
        abort_unless($fornitore->tenant_id === $this->tenantId(), 403);
        $dati = $request->validate(['ragione_sociale' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'pec' => 'nullable|email|max:255', 'partita_iva' => 'nullable|string|max:20', 'indirizzo' => 'nullable|string|max:255', 'citta' => 'nullable|string|max:100', 'provincia' => 'nullable|string|max:5', 'cap' => 'nullable|string|max:10', 'telefono' => 'nullable|string|max:20', 'codice_sdi' => 'nullable|string|max:10', 'note' => 'nullable|string']);
        $fornitore->update($dati);
        return redirect()->route('fornitori.show', $fornitore)->with('successo', 'Fornitore aggiornato.');
    }

    public function destroy(Fornitore $fornitore)
    {
        abort_unless($fornitore->tenant_id === $this->tenantId(), 403);
        $fornitore->delete();
        return redirect()->route('fornitori.index')->with('successo', 'Fornitore eliminato.');
    }
}
