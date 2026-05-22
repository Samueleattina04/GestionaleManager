<?php
namespace App\Http\Controllers;

use App\Models\Fattura;
use App\Models\Intervento;
use App\Models\Articolo;
use App\Models\MovimentoMagazzino;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index()
    {
        return view('report.index');
    }

    public function fatturato(Request $request)
    {
        $anno = $request->input('anno', now()->year);

        $fatture = Fattura::where('tenant_id', $this->tenantId())
            ->whereIn('tipo', ['fattura'])
            ->whereYear('data_emissione', $anno)
            ->get();

        $dati = [];
        for ($m = 1; $m <= 12; $m++) {
            $meseFatture = $fatture->filter(fn($f) => $f->data_emissione?->month === $m);
            $dati[] = [
                'mese' => $m,
                'fatturato' => $meseFatture->sum('totale'),
                'incassato' => $meseFatture->sum('pagato'),
                'count' => $meseFatture->count(),
            ];
        }

        $totale_fatturato = $fatture->sum('totale');
        $totale_incassato = $fatture->sum('pagato');

        return view('report.fatturato', compact('dati', 'anno', 'totale_fatturato', 'totale_incassato'));
    }

    public function interventi(Request $request)
    {
        $tenantId = $this->tenantId();
        $da = $request->input('da', now()->startOfMonth()->format('Y-m-d'));
        $a = $request->input('a', now()->format('Y-m-d'));
        $tecnicoId = $request->input('tecnico_id');

        $query = Intervento::where('tenant_id', $tenantId)->whereBetween(
            DB::raw('DATE(data_pianificata)'), [$da, $a]
        );
        if ($tecnicoId) $query->where('tecnico_id', $tecnicoId);
        $interventi = $query->get();

        $conteggiStati = $interventi->groupBy('stato')->map->count()->toArray();

        $datiMese = DB::table('interventi')
            ->select(DB::raw('MONTH(data_pianificata) as mese'), DB::raw('COUNT(*) as count'))
            ->where('tenant_id', $tenantId)
            ->whereYear('data_pianificata', now()->year)
            ->groupBy(DB::raw('MONTH(data_pianificata)'))
            ->orderBy('mese')
            ->get()->toArray();

        $tecnici = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->where('name', 'tecnico'))
            ->withCount(['interventi as interventi_totali' => fn($q) => $q->whereBetween(DB::raw('DATE(data_pianificata)'), [$da, $a])])
            ->withCount(['interventi as interventi_completati' => fn($q) => $q->where('stato', 'completato')->whereBetween(DB::raw('DATE(data_pianificata)'), [$da, $a])])
            ->get();

        return view('report.interventi', compact('conteggiStati', 'datiMese', 'tecnici', 'da', 'a'));
    }

    public function magazzino()
    {
        $tenantId = $this->tenantId();

        $articoli = Articolo::where('tenant_id', $tenantId)
            ->with(['giacenze.magazzino', 'categoria'])
            ->get();

        $valore_totale = $articoli->sum(fn($a) => $a->giacenzaTotale() * ($a->prezzo_acquisto ?? 0));
        $sotto_scorta = $articoli->filter(fn($a) => $a->isSottoScortaMinima())->count();
        $movimenti_mese = MovimentoMagazzino::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)->count();

        $categorie = DB::table('articoli')
            ->leftJoin('categorie_articoli', 'articoli.categoria_id', '=', 'categorie_articoli.id')
            ->leftJoin('giacenze', 'articoli.id', '=', 'giacenze.articolo_id')
            ->where('articoli.tenant_id', $tenantId)
            ->select(
                DB::raw('COALESCE(categorie_articoli.nome, "Senza categoria") as nome'),
                DB::raw('SUM(COALESCE(giacenze.quantita, 0) * articoli.prezzo_acquisto) as valore')
            )
            ->groupBy('categorie_articoli.id', 'categorie_articoli.nome')
            ->get();

        $top_articoli = $articoli->sortByDesc(fn($a) => $a->giacenzaTotale() * ($a->prezzo_acquisto ?? 0))->take(10);

        return view('report.magazzino', compact('valore_totale', 'sotto_scorta', 'movimenti_mese', 'categorie', 'top_articoli'));
    }

    public function esporta(Request $request)
    {
        return back()->with('successo', 'Export in elaborazione. Il file sarà disponibile a breve.');
    }
}
