<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function mostraRichiesta()
    {
        return view('auth.password-richiesta');
    }

    public function inviaReset(Request $request)
    {
        $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'L\'email è obbligatoria.',
        ]);

        $stato = Password::sendResetLink($request->only('email'));

        return $stato === Password::RESET_LINK_SENT
            ? back()->with('stato', 'Ti abbiamo inviato un link per reimpostare la password!')
            : back()->withErrors(['email' => 'Non è stato possibile trovare un account con questa email.']);
    }

    public function mostraReset(Request $request, string $token)
    {
        return view('auth.password-reset', ['token' => $token, 'email' => $request->email]);
    }

    public function aggiorna(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'password.confirmed' => 'Le password non coincidono.',
        ]);

        $stato = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return $stato === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('stato', 'Password reimpostata con successo!')
            : back()->withErrors(['email' => 'Link di reset non valido o scaduto.']);
    }
}
