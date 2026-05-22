<?php
namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentificaTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // Identifica tenant da utente autenticato
        if (Auth::check() && !Auth::user()->is_super_admin) {
            $tenant = Auth::user()->tenant;
        }

        if ($tenant && !$tenant->attivo) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Il tuo account è stato disattivato. Contatta il supporto.']);
        }

        if ($tenant) {
            app()->instance('tenant', $tenant);
            view()->share('tenant', $tenant);
        }

        return $next($request);
    }
}
