<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FornitoreController;
use App\Http\Controllers\ArticoloController;
use App\Http\Controllers\MagazzinoController;
use App\Http\Controllers\InterventoController;
use App\Http\Controllers\PreventivoController;
use App\Http\Controllers\FatturaController;
use App\Http\Controllers\ContrattoController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\ProfiloController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ImpostazioniController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;

// Homepage
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->is_super_admin) {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Autenticazione
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'mostra'])->name('login');
    Route::post('/login', [LoginController::class, 'accedi'])->name('login.post');
    Route::get('/registrazione', [RegisterController::class, 'mostra'])->name('register');
    Route::post('/registrazione', [RegisterController::class, 'registra'])->name('register.post');
    Route::get('/password/reset', [PasswordController::class, 'mostraRichiesta'])->name('password.request');
    Route::post('/password/reset', [PasswordController::class, 'inviaReset'])->name('password.email');
    Route::get('/password/reset/{token}', [PasswordController::class, 'mostraReset'])->name('password.reset');
    Route::post('/password/aggiorna', [PasswordController::class, 'aggiorna'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'esci'])->name('logout')->middleware('auth');

// Area Super Admin
Route::middleware(['auth', 'super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('tenant', SuperAdminTenantController::class);
    Route::patch('tenant/{tenant}/attiva', [SuperAdminTenantController::class, 'attiva'])->name('tenant.attiva');
    Route::patch('tenant/{tenant}/disattiva', [SuperAdminTenantController::class, 'disattiva'])->name('tenant.disattiva');
    Route::get('/statistiche', [SuperAdminController::class, 'statistiche'])->name('statistiche');
    Route::get('/log', [SuperAdminController::class, 'log'])->name('log');
});

// Area Tenant (utenti aziendali)
Route::middleware(['auth', 'verifica_tenant', 'identifica_tenant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profilo
    Route::get('/profilo', [ProfiloController::class, 'index'])->name('profilo');
    Route::put('/profilo', [ProfiloController::class, 'aggiorna'])->name('profilo.aggiorna');
    Route::put('/profilo/password', [ProfiloController::class, 'aggiornaPassword'])->name('profilo.password');

    // Impostazioni azienda
    Route::middleware('role:titolare')->group(function () {
        Route::get('/impostazioni', [ImpostazioniController::class, 'index'])->name('impostazioni');
        Route::put('/impostazioni', [ImpostazioniController::class, 'aggiorna'])->name('impostazioni.aggiorna');
        Route::get('/impostazioni/utenti', [ImpostazioniController::class, 'utenti'])->name('impostazioni.utenti');
        Route::post('/impostazioni/utenti', [ImpostazioniController::class, 'creaUtente'])->name('impostazioni.utenti.crea');
        Route::put('/impostazioni/utenti/{user}', [ImpostazioniController::class, 'aggiornaUtente'])->name('impostazioni.utenti.aggiorna');
        Route::delete('/impostazioni/utenti/{user}', [ImpostazioniController::class, 'eliminaUtente'])->name('impostazioni.utenti.elimina');
    });

    // Clienti
    Route::middleware('permission:visualizza_clienti')->group(function () {
        Route::resource('clienti', ClienteController::class);
        Route::get('clienti/{cliente}/storico', [ClienteController::class, 'storico'])->name('clienti.storico');
    });

    // Fornitori
    Route::middleware('permission:visualizza_clienti')->group(function () {
        Route::resource('fornitori', FornitoreController::class);
    });

    // Magazzino
    Route::middleware('permission:visualizza_magazzino')->group(function () {
        Route::resource('magazzini', MagazzinoController::class);
        Route::resource('articoli', ArticoloController::class);
        Route::get('articoli/{articolo}/movimenti', [ArticoloController::class, 'movimenti'])->name('articoli.movimenti');
        Route::post('magazzino/movimento', [MagazzinoController::class, 'registraMovimento'])->name('magazzino.movimento');
        Route::get('magazzino/inventario', [MagazzinoController::class, 'inventario'])->name('magazzino.inventario');
        Route::get('magazzino/scarti', [MagazzinoController::class, 'articoliSottoScorta'])->name('magazzino.sotto-scorta');
    });

    // Interventi
    Route::middleware('permission:visualizza_interventi')->group(function () {
        Route::resource('interventi', InterventoController::class);
        Route::post('interventi/{intervento}/assegna', [InterventoController::class, 'assegna'])->name('interventi.assegna');
        Route::post('interventi/{intervento}/cambia-stato', [InterventoController::class, 'cambiaStato'])->name('interventi.cambia-stato');
        Route::post('interventi/{intervento}/foto', [InterventoController::class, 'caricaFoto'])->name('interventi.foto');
        Route::delete('interventi/{intervento}/foto/{foto}', [InterventoController::class, 'eliminaFoto'])->name('interventi.foto.elimina');
        Route::post('interventi/{intervento}/checklist', [InterventoController::class, 'aggiungiChecklist'])->name('interventi.checklist');
        Route::patch('interventi/{intervento}/checklist/{item}', [InterventoController::class, 'aggiornaChecklist'])->name('interventi.checklist.aggiorna');
        Route::post('interventi/{intervento}/timer/start', [InterventoController::class, 'startTimer'])->name('interventi.timer.start');
        Route::post('interventi/{intervento}/timer/stop', [InterventoController::class, 'stopTimer'])->name('interventi.timer.stop');
        Route::post('interventi/{intervento}/firma', [InterventoController::class, 'salvaFirma'])->name('interventi.firma');
        Route::post('interventi/{intervento}/converti-fattura', [InterventoController::class, 'convertiFattura'])->name('interventi.converti-fattura');
        Route::get('interventi/{intervento}/pdf', [InterventoController::class, 'pdf'])->name('interventi.pdf');
    });

    // Pannello Tecnico
    Route::middleware('role:tecnico')->prefix('tecnico')->name('tecnico.')->group(function () {
        Route::get('/', [TecnicoController::class, 'dashboard'])->name('dashboard');
        Route::get('/interventi', [TecnicoController::class, 'interventi'])->name('interventi');
        Route::get('/interventi/{intervento}', [TecnicoController::class, 'dettaglioIntervento'])->name('intervento');
        Route::get('/magazzino', [TecnicoController::class, 'magazzino'])->name('magazzino');
        Route::post('/magazzino/usa', [TecnicoController::class, 'usaArticolo'])->name('usa-articolo');
        Route::post('/disponibilita', [TecnicoController::class, 'toggleDisponibilita'])->name('disponibilita');
        Route::post('/posizione', [TecnicoController::class, 'aggiornaPosizone'])->name('posizione');
    });

    // GPS
    Route::prefix('gps')->name('gps.')->group(function () {
        Route::post('/posizione', [GpsController::class, 'aggiornaPosizione'])->name('posizione');
        Route::get('/mappa', [GpsController::class, 'mappa'])->name('mappa');
        Route::get('/storico/{user}', [GpsController::class, 'storico'])->name('storico');
    });

    // Preventivi
    Route::middleware('permission:visualizza_preventivi')->group(function () {
        Route::resource('preventivi', PreventivoController::class);
        Route::post('preventivi/{preventivo}/invia', [PreventivoController::class, 'invia'])->name('preventivi.invia');
        Route::post('preventivi/{preventivo}/converti-intervento', [PreventivoController::class, 'convertiIntervento'])->name('preventivi.converti-intervento');
        Route::post('preventivi/{preventivo}/converti-fattura', [PreventivoController::class, 'convertiFattura'])->name('preventivi.converti-fattura');
        Route::get('preventivi/{preventivo}/pdf', [PreventivoController::class, 'pdf'])->name('preventivi.pdf');
    });

    // Fatture
    Route::middleware('permission:visualizza_fatture')->group(function () {
        Route::resource('fatture', FatturaController::class);
        Route::post('fatture/{fattura}/pagamento', [FatturaController::class, 'registraPagamento'])->name('fatture.pagamento');
        Route::get('fatture/{fattura}/pdf', [FatturaController::class, 'pdf'])->name('fatture.pdf');
        Route::get('fatture/{fattura}/xml', [FatturaController::class, 'xml'])->name('fatture.xml');
        Route::get('registro-iva', [FatturaController::class, 'registroIva'])->name('fatture.registro-iva');
        Route::get('scadenzario', [FatturaController::class, 'scadenzario'])->name('fatture.scadenzario');
    });

    // Contratti
    Route::middleware('permission:visualizza_contratti')->group(function () {
        Route::resource('contratti', ContrattoController::class);
        Route::post('contratti/{contratto}/rinnova', [ContrattoController::class, 'rinnova'])->name('contratti.rinnova');
        Route::get('contratti/{contratto}/manutenzioni', [ContrattoController::class, 'manutenzioni'])->name('contratti.manutenzioni');
        Route::post('contratti/{contratto}/genera-manutenzioni', [ContrattoController::class, 'generaManutenzioniPubbliche'])->name('contratti.genera-manutenzioni');
    });

    // Notifiche
    Route::prefix('notifiche')->name('notifiche.')->group(function () {
        Route::get('/', [NotificaController::class, 'index'])->name('index');
        Route::post('/{notifica}/leggi', [NotificaController::class, 'segnaLetta'])->name('leggi');
        Route::post('/leggi-tutte', [NotificaController::class, 'segnaLetteTutte'])->name('leggi-tutte');
        Route::get('/preferenze', [NotificaController::class, 'preferenze'])->name('preferenze');
        Route::put('/preferenze', [NotificaController::class, 'aggiornaPreferenze'])->name('preferenze.aggiorna');
    });

    // Report
    Route::middleware('permission:visualizza_report')->prefix('report')->name('report.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/fatturato', [ReportController::class, 'fatturato'])->name('fatturato');
        Route::get('/interventi', [ReportController::class, 'interventi'])->name('interventi');
        Route::get('/magazzino', [ReportController::class, 'magazzino'])->name('magazzino');
        Route::post('/esporta', [ReportController::class, 'esporta'])->name('esporta');
    });
});
