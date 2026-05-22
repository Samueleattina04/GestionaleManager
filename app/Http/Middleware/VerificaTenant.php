<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificaTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->is_super_admin) {
            return redirect()->route('superadmin.dashboard');
        }

        if (!Auth::user()->tenant_id || !Auth::user()->tenant) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Nessuna azienda associata a questo account.']);
        }

        return $next($request);
    }
}
