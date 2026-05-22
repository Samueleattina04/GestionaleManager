<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function mostra()
    {
        return view('auth.login');
    }

    public function accedi(Request $request)
    {
        $credenziali = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'password.required' => 'La password è obbligatoria.',
        ]);

        if (Auth::attempt($credenziali, $request->boolean('ricordami'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->attivo) {
                Auth::logout();
                return back()->withErrors(['email' => 'Il tuo account è stato disattivato.'])->onlyInput('email');
            }

            if ($user->is_super_admin) {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            if ($user->hasRole('tecnico')) {
                return redirect()->intended(route('tecnico.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Le credenziali inserite non sono corrette.',
        ])->onlyInput('email');
    }

    public function esci(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
