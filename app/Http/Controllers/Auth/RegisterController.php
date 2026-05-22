<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Magazzino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function mostra()
    {
        return view('auth.registrazione');
    }

    public function registra(Request $request)
    {
        $request->validate([
            'ragione_sociale' => ['required', 'string', 'max:255'],
            'nome_utente' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accetta_termini' => ['required', 'accepted'],
        ], [
            'ragione_sociale.required' => 'La ragione sociale è obbligatoria.',
            'nome_utente.required' => 'Il nome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.unique' => 'Questa email è già registrata.',
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'password.confirmed' => 'Le password non coincidono.',
            'accetta_termini.required' => 'Devi accettare i termini e condizioni.',
        ]);

        DB::transaction(function () use ($request) {
            $slug = Str::slug($request->ragione_sociale);
            $slugBase = $slug;
            $i = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $i++;
            }

            $tenant = Tenant::create([
                'nome' => $request->ragione_sociale,
                'slug' => $slug,
                'ragione_sociale' => $request->ragione_sociale,
                'email' => $request->email,
                'attivo' => true,
                'piano' => 'base',
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->nome_utente,
                'email' => $request->email,
                'password' => $request->password,
                'attivo' => true,
                'email_verified_at' => now(),
            ]);

            // Assegna ruolo titolare
            $user->assignRole('titolare');

            // Crea magazzino principale
            Magazzino::create([
                'tenant_id' => $tenant->id,
                'nome' => 'Magazzino Principale',
                'principale' => true,
                'mobile' => false,
                'attivo' => true,
            ]);

            Auth::login($user);
        });

        return redirect()->route('dashboard')->with('successo', 'Benvenuto! Il tuo account è stato creato con successo.');
    }
}
