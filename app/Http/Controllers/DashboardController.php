<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fattura;
use App\Models\Intervento;
use App\Models\Articolo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        $oggi = Carbon::today();
        $inizioMese = Carbon::now()->startOfMonth();
        $fineMese = Carbon::now()->endOfMonth();
        $inizioMesePrec = Carbon::now()->subMonth()->startOfMonth();
        $fineMesePrec = Carbon::now()->subMonth()->endOfMonth();

        // Fatturato mese corrente
        $fatturatoMese = Fattura::where('tenant_id', $tenantId)
            ->whereIn('stato', ['emessa', 'pagata_parzialmente', 'pagata'])
            ->whereBetween('data', [$inizioMese, $fineMese])
            ->where('tipo', 'fattura')
            ->sum('totale');

        // Fatturato mese precedente
        $fatturatoMesePrec = Fattura::where('tenant_id', $tenantId)
            ->whereIn('stato', ['emessa', 'pagata_parzialmente', 'pagata'])
            ->whereBetween('data', [$inizioMesePrec, $fineMesePrec])
            ->where('tipo', 'fattura')
            ->sum('totale');

        // Interventi
        $interventiAperti = Intervento::where('tenant_id', $tenantId)
            ->where('stato', 'da_assegnare')->count();
        $interventiInCorso = Intervento::where('tenant_id', $tenantId)
            ->where('stato', 'in_corso')->count();
        $interventiOggi = Intervento::where('tenant_id', $tenantId)
            ->where('stato', 'completato')
            ->whereDate('data_fine_effettivo', $oggi)->count();

        // Articoli sotto scorta
        $sottoScorta = Articolo::where('tenant_id', $tenantId)
            ->where('attivo', true)
            ->whereHas('giacenze', function ($q) {
                $q->whereRaw('quantita <= (SELECT scorta_minima FROM articoli WHERE articoli.id = giacenze.articolo_id)');
            })->count();

        // Ultimi 5 interventi
        $ultimiInterventi = Intervento::where('tenant_id', $tenantId)
            ->with(['cliente', 'tecnico'])
            ->latest()
            ->limit(5)
            ->get();

        // Prossime scadenze fatture
        $scadenze = Fattura::where('tenant_id', $tenantId)
            ->whereIn('stato', ['emessa', 'pagata_parzialmente'])
            ->where('data_scadenza', '>=', $oggi)
            ->where('data_scadenza', '<=', $oggi->copy()->addDays(30))
            ->with('cliente')
            ->orderBy('data_scadenza')
            ->limit(5)
            ->get();

        // Grafico fatturato ultimi 6 mesi
        $graficoDati = [];
        for ($i = 5; $i >= 0; $i--) {
            $mese = Carbon::now()->subMonths($i);
            $graficoDati[] = [
                'mese' => $mese->translatedFormat('M Y'),
                'totale' => Fattura::where('tenant_id', $tenantId)
                    ->whereIn('stato', ['emessa', 'pagata_parzialmente', 'pagata'])
                    ->where('tipo', 'fattura')
                    ->whereYear('data', $mese->year)
                    ->whereMonth('data', $mese->month)
                    ->sum('totale'),
            ];
        }

        // Tecnici attivi
        $tecniciAttivi = User::where('tenant_id', $tenantId)
            ->role('tecnico')
            ->whereHas('statoTecnico', fn($q) => $q->where('disponibile', true))
            ->with('statoTecnico')
            ->get();

        return view('dashboard', compact(
            'fatturatoMese', 'fatturatoMesePrec',
            'interventiAperti', 'interventiInCorso', 'interventiOggi',
            'sottoScorta', 'ultimiInterventi', 'scadenze',
            'graficoDati', 'tecniciAttivi'
        ));
    }
}
