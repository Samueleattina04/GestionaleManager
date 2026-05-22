<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clienti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('codice', 20)->nullable();
            $table->enum('tipo', ['privato', 'azienda'])->default('azienda');
            $table->string('ragione_sociale');
            $table->string('nome')->nullable();
            $table->string('cognome')->nullable();
            $table->string('partita_iva', 20)->nullable();
            $table->string('codice_fiscale', 20)->nullable();
            $table->string('indirizzo')->nullable();
            $table->string('citta')->nullable();
            $table->string('provincia', 5)->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('paese', 50)->default('IT');
            $table->decimal('latitudine', 10, 8)->nullable();
            $table->decimal('longitudine', 11, 8)->nullable();
            $table->string('referente')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('cellulare', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('pec')->nullable();
            $table->string('codice_sdi', 10)->nullable();
            $table->text('note')->nullable();
            $table->json('campi_personalizzati')->nullable();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'ragione_sociale']);
        });

        Schema::create('fornitori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('codice', 20)->nullable();
            $table->string('ragione_sociale');
            $table->string('partita_iva', 20)->nullable();
            $table->string('codice_fiscale', 20)->nullable();
            $table->string('indirizzo')->nullable();
            $table->string('citta')->nullable();
            $table->string('provincia', 5)->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('paese', 50)->default('IT');
            $table->string('referente')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('pec')->nullable();
            $table->string('codice_sdi', 10)->nullable();
            $table->text('note')->nullable();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'ragione_sociale']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('fornitori');
        Schema::dropIfExists('clienti');
    }
};
