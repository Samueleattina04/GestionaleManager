<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\LogAttivita;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $tenantAttivi = Tenant::where('attivo', true)->count();
        $tenantTotali = Tenant::count();
        $utentiTotali = User::where('is_super_admin', false)->count();
        $ultimiTenant = Tenant::latest()->limit(10)->get();

        return view('superadmin.dashboard', compact(
            'tenantAttivi', 'tenantTotali', 'utentiTotali', 'ultimiTenant'
        ));
    }

    public function statistiche()
    {
        $tenant = Tenant::withCount('utenti')->latest()->get();
        return view('superadmin.statistiche', compact('tenant'));
    }

    public function log()
    {
        $logs = LogAttivita::with(['utente', 'tenant'])
            ->latest()
            ->paginate(50);
        return view('superadmin.log', compact('logs'));
    }
}
