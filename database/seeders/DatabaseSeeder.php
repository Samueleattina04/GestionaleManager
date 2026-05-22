<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Permessi di sistema
        $permessi = [
            'visualizza_dashboard',
            'visualizza_clienti', 'crea_clienti', 'modifica_clienti', 'elimina_clienti',
            'visualizza_magazzino', 'crea_movimenti', 'modifica_articoli', 'elimina_articoli',
            'visualizza_interventi', 'crea_interventi', 'modifica_interventi', 'elimina_interventi', 'assegna_interventi',
            'visualizza_preventivi', 'crea_preventivi', 'modifica_preventivi', 'elimina_preventivi',
            'visualizza_fatture', 'crea_fatture', 'modifica_fatture', 'elimina_fatture', 'registra_pagamenti',
            'visualizza_contratti', 'crea_contratti', 'modifica_contratti', 'elimina_contratti',
            'visualizza_report', 'esporta_report',
            'gestisci_utenti', 'gestisci_impostazioni',
        ];

        foreach ($permessi as $permesso) {
            Permission::firstOrCreate(['name' => $permesso, 'guard_name' => 'web']);
        }

        // Ruolo: Titolare - tutti i permessi
        $titolare = Role::firstOrCreate(['name' => 'titolare', 'guard_name' => 'web']);
        $titolare->syncPermissions($permessi);

        // Ruolo: Amministratore/Contabile
        $amministratore = Role::firstOrCreate(['name' => 'amministratore', 'guard_name' => 'web']);
        $amministratore->syncPermissions([
            'visualizza_dashboard', 'visualizza_fatture', 'crea_fatture',
            'modifica_fatture', 'registra_pagamenti', 'visualizza_report', 'esporta_report',
            'visualizza_preventivi', 'crea_preventivi', 'modifica_preventivi',
        ]);

        // Ruolo: Magazziniere
        $magazziniere = Role::firstOrCreate(['name' => 'magazziniere', 'guard_name' => 'web']);
        $magazziniere->syncPermissions([
            'visualizza_dashboard', 'visualizza_magazzino', 'crea_movimenti',
            'modifica_articoli', 'visualizza_interventi',
        ]);

        // Ruolo: Commerciale
        $commerciale = Role::firstOrCreate(['name' => 'commerciale', 'guard_name' => 'web']);
        $commerciale->syncPermissions([
            'visualizza_dashboard', 'visualizza_clienti', 'crea_clienti', 'modifica_clienti',
            'visualizza_preventivi', 'crea_preventivi', 'modifica_preventivi',
            'visualizza_fatture',
        ]);

        // Ruolo: Tecnico
        $tecnico = Role::firstOrCreate(['name' => 'tecnico', 'guard_name' => 'web']);
        $tecnico->syncPermissions([
            'visualizza_dashboard', 'visualizza_interventi', 'modifica_interventi',
            'visualizza_magazzino', 'crea_movimenti',
        ]);

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gestionale.it'],
            [
                'name' => 'Super Amministratore',
                'password' => bcrypt('superadmin123'),
                'is_super_admin' => true,
                'attivo' => true,
                'email_verified_at' => now(),
            ]
        );

        // Tenant demo
        $tenantDemo = Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'nome' => 'Demo Azienda',
                'ragione_sociale' => 'Demo Azienda S.r.l.',
                'partita_iva' => '12345678901',
                'email' => 'demo@gestionale.it',
                'citta' => 'Milano',
                'provincia' => 'MI',
                'attivo' => true,
                'piano' => 'premium',
                'colore_primario' => '#2563eb',
                'colore_secondario' => '#1e40af',
            ]
        );

        // Titolare demo
        $titolareDemo = User::firstOrCreate(
            ['email' => 'titolare@demo.it'],
            [
                'tenant_id' => $tenantDemo->id,
                'name' => 'Mario Rossi',
                'password' => bcrypt('password'),
                'attivo' => true,
                'email_verified_at' => now(),
            ]
        );
        $titolareDemo->assignRole('titolare');

        // Tecnico demo
        $tecnicoDemo = User::firstOrCreate(
            ['email' => 'tecnico@demo.it'],
            [
                'tenant_id' => $tenantDemo->id,
                'name' => 'Luigi Bianchi',
                'password' => bcrypt('password'),
                'attivo' => true,
                'email_verified_at' => now(),
            ]
        );
        $tecnicoDemo->assignRole('tecnico');

        // Magazzino principale demo
        \App\Models\Magazzino::firstOrCreate(
            ['tenant_id' => $tenantDemo->id, 'principale' => true],
            [
                'nome' => 'Magazzino Principale',
                'descrizione' => 'Deposito centrale',
                'principale' => true,
                'mobile' => false,
                'attivo' => true,
            ]
        );

        // Magazzino mobile per il tecnico
        \App\Models\Magazzino::firstOrCreate(
            ['tenant_id' => $tenantDemo->id, 'responsabile_id' => $tecnicoDemo->id],
            [
                'nome' => 'Furgone Luigi',
                'descrizione' => 'Magazzino mobile tecnico',
                'principale' => false,
                'mobile' => true,
                'responsabile_id' => $tecnicoDemo->id,
                'attivo' => true,
            ]
        );

        $this->command->info('Database popolato con successo!');
        $this->command->info('Super Admin: admin@gestionale.it / superadmin123');
        $this->command->info('Titolare demo: titolare@demo.it / password');
        $this->command->info('Tecnico demo: tecnico@demo.it / password');
    }
}
