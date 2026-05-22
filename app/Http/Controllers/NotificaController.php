<?php
namespace App\Http\Controllers;

use App\Models\Notifica;
use App\Models\PreferenzaNotifica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificaController extends Controller
{
    public function index()
    {
        $notifiche = Notifica::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);
        return view('notifiche.index', compact('notifiche'));
    }

    public function segnaLetta(Notifica $notifica)
    {
        abort_unless($notifica->user_id === Auth::id(), 403);
        $notifica->segnaLetta();
        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }

    public function segnaLetteTutte()
    {
        Notifica::where('user_id', Auth::id())
            ->where('letta', false)
            ->update(['letta' => true, 'letta_il' => now()]);
        return back()->with('successo', 'Tutte le notifiche segnate come lette.');
    }

    public function preferenze()
    {
        return view('notifiche.preferenze');
    }

    public function aggiornaPreferenze(Request $request)
    {
        return back()->with('successo', 'Preferenze aggiornate.');
    }
}
