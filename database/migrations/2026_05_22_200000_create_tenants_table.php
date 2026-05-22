<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('ragione_sociale');
            $table->string('partita_iva', 20)->nullable();
            $table->string('codice_fiscale', 20)->nullable();
            $table->string('indirizzo')->nullable();
            $table->string('citta')->nullable();
            $table->string('provincia', 5)->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('paese', 50)->default('IT');
            $table->string('telefono', 20)->nullable();
            $table->string('email');
            $table->string('pec')->nullable();
            $table->string('codice_sdi', 10)->nullable();
            $table->string('logo')->nullable();
            $table->string('colore_primario', 20)->default('#2563eb');
            $table->string('colore_secondario', 20)->default('#1e40af');
            $table->boolean('attivo')->default(true);
            $table->string('piano')->default('base');
            $table->date('scadenza_abbonamento')->nullable();
            $table->decimal('importo_abbonamento', 10, 2)->default(0);
            $table->json('impostazioni')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
