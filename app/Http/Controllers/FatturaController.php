<?php
namespace App\Http\Controllers;

use App\Models\Fattura;
use App\Models\FatturaRiga;
use App\Models\Pagamento;
use App\Models\Cliente;
use App\Models\Fornitore;
use App\Models\Articolo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FatturaController extends Controller
{
    private function tenantId(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Fattura::where('tenant_id', $this->tenantId())->with(['cliente', 'fornitore']);
        if ($request->filled('stato')) $query->where('stato', $request->stato);
        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('anno')) $query->where('anno', $request->anno);
        $fatture = $query->orderBy('anno', 'desc')->orderBy('numero', 'desc')->paginate(20)->withQueryString();
        $anni = Fattura::where('tenant_id', $this->tenantId())->distinct()->pluck('anno')->sortDesc();
        return view('fatture.index', compact('fatture', 'anni'));
    }

    public function create()
    {
        $clienti = Cliente::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('ragione_sociale')->get();
        $fornitori = Fornitore::where('tenant_id', $this->tenantId())->orderBy('ragione_sociale')->get();
        $articoli = Articolo::where('tenant_id', $this->tenantId())->where('attivo', true)->orderBy('descrizione')->get();
        return view('fatture.create', compact('clienti', 'fornitori', 'articoli'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:fattura,nota_credito,ddt,fattura_acquisto',
            'data' => 'required|date',
            'righe' => 'required|array|min:1',
            'righe.*.descrizione' => 'required|string',
            'righe.*.quantita' => 'required|numeric|min:0.001',
            'righe.*.prezzo_unitario' => 'required|numeric|min:0',
        ], ['righe.required' => 'Aggiungi almeno una riga.']);

        $anno = Carbon::parse($request->data)->year;
        $ultimo = Fattura::where('tenant_id', $this->tenantId())->where('anno', $anno)->where('tipo', $request->tipo)->max('numero');
        $num = $ultimo ? (intval(substr($ultimo, -4)) + 1) : 1;
        $numStr = $anno . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $numStr, $anno) {
            $totImponibile = 0;
            $totIva = 0;

            $fattura = Fattura::create([
                'tenant_id' => $this->tenantId(),
                'numero' => $numStr,
                'anno' => $anno,
                'tipo' => $request->tipo,
                'cliente_id' => $request->cliente_id,
                'fornitore_id' => $request->fornitore_id,
                'data' => $request->data,
                'data_scadenza' => $request->data_scadenza,
                'stato' => 'bozza',
                'modalita_pagamento' => $request->modalita_pagamento,
                'note' => $request->note,
                'totale_imponibile' => 0,
                'totale_iva' => 0,
                'totale' => 0,
            ]);

            foreach ($request->righe as $i => $riga) {
                $imp = $riga['quantita'] * $riga['prezzo_unitario'] * (1 - ($riga['sconto'] ?? 0) / 100);
                $iva = $imp * (($riga['iva'] ?? 22) / 100);
                FatturaRiga::create([
                    'tenant_id' => $this->tenantId(),
                    'fattura_id' => $fattura->id,
                    'articolo_id' => $riga['articolo_id'] ?? null,
                    'descrizione' => $riga['descrizione'],
                    'unita_misura' => $riga['unita_misura'] ?? 'pz',
                    'quantita' => $riga['quantita'],
                    'prezzo_unitario' => $riga['prezzo_unitario'],
                    'sconto' => $riga['sconto'] ?? 0,
                    'iva' => $riga['iva'] ?? 22,
                    'totale' => $imp + $iva,
                    'ordine' => $i,
                ]);
                $totImponibile += $imp;
                $totIva += $iva;
            }

            $fattura->update([
                'totale_imponibile' => $totImponibile,
                'totale_iva' => $totIva,
                'totale' => $totImponibile + $totIva,
                'stato' => 'emessa',
            ]);
        });

        return redirect()->route('fatture.index')->with('successo', 'Fattura creata con successo.');
    }

    public function show(Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        $fattura->load(['cliente', 'fornitore', 'righe.articolo', 'pagamenti.registratoDa']);
        return view('fatture.show', compact('fattura'));
    }

    public function edit(Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        return view('fatture.edit', compact('fattura'));
    }

    public function update(Request $request, Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        $fattura->update($request->only(['stato', 'data_scadenza', 'note', 'modalita_pagamento']));
        return redirect()->route('fatture.show', $fattura)->with('successo', 'Fattura aggiornata.');
    }

    public function destroy(Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        if ($fattura->stato === 'pagata') {
            return back()->with('errore', 'Non è possibile eliminare una fattura già pagata.');
        }
        $fattura->delete();
        return redirect()->route('fatture.index')->with('successo', 'Fattura eliminata.');
    }

    public function registraPagamento(Request $request, Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        $dati = $request->validate([
            'importo' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'metodo' => 'nullable|string|max:50',
            'riferimento' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($fattura, $dati) {
            Pagamento::create([
                ...$dati,
                'tenant_id' => $this->tenantId(),
                'fattura_id' => $fattura->id,
                'registrato_da' => Auth::id(),
            ]);

            $totalePagato = $fattura->pagamenti()->sum('importo') + $dati['importo'];
            $stato = $totalePagato >= $fattura->totale ? 'pagata' : 'pagata_parzialmente';
            $fattura->update(['pagato' => $totalePagato, 'stato' => $stato]);
        });

        return back()->with('successo', 'Pagamento registrato con successo.');
    }

    public function pdf(Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        $fattura->load(['cliente', 'fornitore', 'righe']);
        $tenant = Auth::user()->tenant;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.fattura', compact('fattura', 'tenant'));
        return $pdf->download('fattura-' . $fattura->numero . '.pdf');
    }

    public function xml(Fattura $fattura)
    {
        abort_unless($fattura->tenant_id === $this->tenantId(), 403);
        $fattura->load(['cliente', 'righe', 'tenant']);
        $tenant = Auth::user()->tenant;
        $xml = view('xml.fattura-elettronica', compact('fattura', 'tenant'))->render();
        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="fattura-' . $fattura->numero . '.xml"',
        ]);
    }

    public function registroIva(Request $request)
    {
        $anno = $request->input('anno', now()->year);
        $tipo = $request->input('tipo', 'vendite');

        $query = Fattura::where('tenant_id', $this->tenantId())->where('anno', $anno);
        if ($tipo === 'vendite') {
            $query->whereIn('tipo', ['fattura', 'nota_credito', 'ddt'])->whereNotIn('stato', ['bozza', 'annullata']);
        } else {
            $query->where('tipo', 'fattura_acquisto');
        }

        $fatture = $query->with('cliente', 'fornitore')->orderBy('data')->get();
        $anni = Fattura::where('tenant_id', $this->tenantId())->distinct()->pluck('anno')->sortDesc();

        return view('fatture.registro-iva', compact('fatture', 'anno', 'tipo', 'anni'));
    }

    public function scadenzario(Request $request)
    {
        $da = $request->input('da', now()->format('Y-m-d'));
        $a = $request->input('a', now()->addMonths(3)->format('Y-m-d'));

        $fatture = Fattura::where('tenant_id', $this->tenantId())
            ->whereIn('stato', ['emessa', 'pagata_parzialmente'])
            ->whereBetween('data_scadenza', [$da, $a])
            ->with('cliente')
            ->orderBy('data_scadenza')
            ->get();

        return view('fatture.scadenzario', compact('fatture', 'da', 'a'));
    }
}
