<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ImpostazioniController extends Controller
{
    private function tenant() { return Auth::user()->tenant; }

    public function index()
    {
        $tenant = $this->tenant();
        return view('impostazioni.index', compact('tenant'));
    }

    public function aggiorna(Request $request)
    {
        $tenant = $this->tenant();
        $dati = $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'partita_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:5',
            'cap' => 'nullable|string|max:10',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'pec' => 'nullable|email|max:255',
            'codice_sdi' => 'nullable|string|max:10',
            'colore_primario' => 'required|string|max:20',
            'colore_secondario' => 'required|string|max:20',
        ]);

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:2048']);
            $dati['logo'] = $request->file('logo')->store('loghi', 'public');
        }

        $tenant->update($dati);

        return back()->with('successo', 'Impostazioni aziendali salvate con successo.');
    }

    public function utenti()
    {
        $utenti = User::where('tenant_id', Auth::user()->tenant_id)
            ->with('roles')
            ->get();
        $ruoli = Role::all();
        return view('impostazioni.utenti', compact('utenti', 'ruoli'));
    }

    public function creaUtente(Request $request)
    {
        $dati = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'ruolo' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $dati['name'],
            'email' => $dati['email'],
            'password' => $dati['password'],
            'attivo' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole($dati['ruolo']);

        return redirect()->route('impostazioni.utenti')->with('successo', 'Utente creato con successo.');
    }

    public function aggiornaUtente(Request $request, User $user)
    {
        abort_unless($user->tenant_id === Auth::user()->tenant_id, 403);

        $dati = $request->validate([
            'name' => 'required|string|max:255',
            'ruolo' => 'required|exists:roles,name',
            'attivo' => 'boolean',
        ]);

        $user->update(['name' => $dati['name'], 'attivo' => $request->boolean('attivo')]);
        $user->syncRoles([$dati['ruolo']]);

        return redirect()->route('impostazioni.utenti')->with('successo', 'Utente aggiornato.');
    }

    public function eliminaUtente(User $user)
    {
        abort_unless($user->tenant_id === Auth::user()->tenant_id, 403);
        abort_if($user->id === Auth::id(), 403, 'Non puoi eliminare il tuo account.');
        $user->delete();
        return redirect()->route('impostazioni.utenti')->with('successo', 'Utente eliminato.');
    }
}
