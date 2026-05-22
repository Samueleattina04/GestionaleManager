<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    private function tenantId(): int
    {
        return Auth::user()->tenant_id;
    }

    public function index(Request $request)
    {
        $query = Cliente::where('tenant_id', $this->tenantId());

        if ($request->filled('cerca')) {
            $query->where(function ($q) use ($request) {
                $q->where('ragione_sociale', 'like', '%' . $request->cerca . '%')
                  ->orWhere('email', 'like', '%' . $request->cerca . '%')
                  ->orWhere('telefono', 'like', '%' . $request->cerca . '%')
                  ->orWhere('codice', 'like', '%' . $request->cerca . '%');
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->boolean('solo_attivi', true)) {
            $query->where('attivo', true);
        }

        $clienti = $query->orderBy('ragione_sociale')->paginate(20)->withQueryString();

        return view('clienti.index', compact('clienti'));
    }

    public function create()
    {
        return view('clienti.create');
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'tipo' => 'required|in:privato,azienda',
            'ragione_sociale' => 'required|string|max:255',
            'nome' => 'nullable|string|max:100',
            'cognome' => 'nullable|string|max:100',
            'partita_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:5',
            'cap' => 'nullable|string|max:10',
            'referente' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'cellulare' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pec' => 'nullable|email|max:255',
            'codice_sdi' => 'nullable|string|max:10',
            'note' => 'nullable|string',
        ], $this->messaggiValidazione());

        $dati['tenant_id'] = $this->tenantId();

        // Genera codice progressivo
        $ultimoCodice = Cliente::where('tenant_id', $this->tenantId())
            ->where('codice', 'like', 'CLI%')
            ->orderByDesc('codice')->value('codice');
        $numero = $ultimoCodice ? (intval(substr($ultimoCodice, 3)) + 1) : 1;
        $dati['codice'] = 'CLI' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        Cliente::create($dati);

        return redirect()->route('clienti.index')
            ->with('successo', 'Cliente creato con successo.');
    }

    public function show(Cliente $cliente)
    {
        $this->autorizza($cliente);
        $cliente->load(['interventi.tecnico', 'fatture', 'preventivi', 'contratti']);
        return view('clienti.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $this->autorizza($cliente);
        return view('clienti.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $this->autorizza($cliente);

        $dati = $request->validate([
            'tipo' => 'required|in:privato,azienda',
            'ragione_sociale' => 'required|string|max:255',
            'nome' => 'nullable|string|max:100',
            'cognome' => 'nullable|string|max:100',
            'partita_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:5',
            'cap' => 'nullable|string|max:10',
            'referente' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'cellulare' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pec' => 'nullable|email|max:255',
            'codice_sdi' => 'nullable|string|max:10',
            'note' => 'nullable|string',
        ], $this->messaggiValidazione());

        $cliente->update($dati);

        return redirect()->route('clienti.show', $cliente)
            ->with('successo', 'Cliente aggiornato con successo.');
    }

    public function destroy(Cliente $cliente)
    {
        $this->autorizza($cliente);
        $cliente->delete();
        return redirect()->route('clienti.index')
            ->with('successo', 'Cliente eliminato.');
    }

    public function storico(Cliente $cliente)
    {
        $this->autorizza($cliente);
        $cliente->load(['interventi.tecnico', 'fatture', 'preventivi', 'contratti']);
        return view('clienti.storico', compact('cliente'));
    }

    private function autorizza(Cliente $cliente): void
    {
        abort_unless($cliente->tenant_id === $this->tenantId(), 403);
    }

    private function messaggiValidazione(): array
    {
        return [
            'ragione_sociale.required' => 'La ragione sociale è obbligatoria.',
            'tipo.required' => 'Il tipo cliente è obbligatorio.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'pec.email' => 'Inserisci un indirizzo PEC valido.',
        ];
    }
}
