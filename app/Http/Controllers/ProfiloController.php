<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfiloController extends Controller
{
    public function index()
    {
        return view('profilo.index', ['utente' => Auth::user()]);
    }

    public function aggiorna(Request $request)
    {
        $user = Auth::user();
        $dati = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'telefono' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.unique' => 'Questa email è già in uso.',
        ]);

        if ($request->hasFile('avatar')) {
            $request->validate(['avatar' => 'image|max:2048']);
            $dati['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($dati);

        return back()->with('successo', 'Profilo aggiornato con successo.');
    }

    public function aggiornaPassword(Request $request)
    {
        $request->validate([
            'password_attuale' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_attuale.required' => 'Inserisci la password attuale.',
            'password.confirmed' => 'Le password non coincidono.',
        ]);

        if (!Hash::check($request->password_attuale, Auth::user()->password)) {
            return back()->withErrors(['password_attuale' => 'La password attuale non è corretta.']);
        }

        Auth::user()->update(['password' => $request->password]);

        return back()->with('successo', 'Password aggiornata con successo.');
    }
}
