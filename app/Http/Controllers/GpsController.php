<?php
namespace App\Http\Controllers;

use App\Models\PosizioneGps;
use App\Models\StatoTecnico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GpsController extends Controller
{
    public function aggiornaPosizione(Request $request)
    {
        $dati = $request->validate([
            'latitudine' => 'required|numeric|between:-90,90',
            'longitudine' => 'required|numeric|between:-180,180',
            'precisione' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id;

        PosizioneGps::create([
            'tenant_id' => $tenantId,
            'user_id' => $user->id,
            'latitudine' => $dati['latitudine'],
            'longitudine' => $dati['longitudine'],
            'precisione' => $dati['precisione'] ?? null,
            'rilevato_il' => now(),
        ]);

        StatoTecnico::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tenant_id' => $tenantId,
                'ultima_lat' => $dati['latitudine'],
                'ultima_lng' => $dati['longitudine'],
                'ultima_posizione_il' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function mappa()
    {
        $tenantId = Auth::user()->tenant_id;
        $tecnici = User::where('tenant_id', $tenantId)
            ->role('tecnico')
            ->with('statoTecnico')
            ->get();
        return view('gps.mappa', compact('tecnici'));
    }

    public function storico(User $user)
    {
        abort_unless($user->tenant_id === Auth::user()->tenant_id, 403);
        $data = request('data', now()->format('Y-m-d'));
        $posizioni = PosizioneGps::where('user_id', $user->id)
            ->whereDate('rilevato_il', $data)
            ->orderBy('rilevato_il')
            ->get();
        return view('gps.storico', compact('user', 'posizioni', 'data'));
    }
}
