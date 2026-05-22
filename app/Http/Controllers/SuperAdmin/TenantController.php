<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount('utenti')->latest()->paginate(20);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:100|unique:tenants',
            'piano' => 'required|in:base,standard,premium',
        ]);

        Tenant::create($request->all());

        return redirect()->route('superadmin.tenant.index')
            ->with('successo', 'Azienda creata con successo.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('utenti');
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'piano' => 'required|in:base,standard,premium',
        ]);

        $tenant->update($request->all());

        return redirect()->route('superadmin.tenant.index')
            ->with('successo', 'Azienda aggiornata con successo.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('superadmin.tenant.index')
            ->with('successo', 'Azienda eliminata.');
    }

    public function attiva(Tenant $tenant)
    {
        $tenant->update(['attivo' => true]);
        return back()->with('successo', "Azienda '{$tenant->ragione_sociale}' attivata.");
    }

    public function disattiva(Tenant $tenant)
    {
        $tenant->update(['attivo' => false]);
        return back()->with('successo', "Azienda '{$tenant->ragione_sociale}' disattivata.");
    }
}
