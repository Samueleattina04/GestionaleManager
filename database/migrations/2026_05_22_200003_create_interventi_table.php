<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interventi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('numero', 20)->nullable();
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('contratto_id')->nullable();
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->enum('priorita', ['urgente', 'normale', 'bassa'])->default('normale');
            $table->enum('stato', ['da_assegnare', 'assegnato', 'in_corso', 'completato', 'annullato'])->default('da_assegnare');
            $table->dateTime('data_pianificata')->nullable();
            $table->dateTime('data_inizio_effettivo')->nullable();
            $table->dateTime('data_fine_effettivo')->nullable();
            $table->integer('minuti_lavorati')->default(0);
            $table->text('note_interne')->nullable();
            $table->text('note_cliente')->nullable();
            $table->string('firma_cliente')->nullable();
            $table->string('indirizzo_intervento')->nullable();
            $table->decimal('latitudine', 10, 8)->nullable();
            $table->decimal('longitudine', 11, 8)->nullable();
            $table->boolean('fatturato')->default(false);
            $table->foreignId('fattura_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'stato']);
            $table->index(['tenant_id', 'tecnico_id']);
        });

        Schema::create('intervento_articoli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('intervento_id')->constrained('interventi')->cascadeOnDelete();
            $table->foreignId('articolo_id')->constrained('articoli')->cascadeOnDelete();
            $table->foreignId('magazzino_id')->nullable()->constrained('magazzini')->nullOnDelete();
            $table->decimal('quantita', 12, 4);
            $table->decimal('prezzo_unitario', 12, 4)->default(0);
            $table->decimal('sconto', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('intervento_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('intervento_id')->constrained('interventi')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('percorso');
            $table->string('nome_originale')->nullable();
            $table->text('descrizione')->nullable();
            $table->timestamps();
        });

        Schema::create('intervento_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('intervento_id')->constrained('interventi')->cascadeOnDelete();
            $table->string('voce');
            $table->boolean('completata')->default(false);
            $table->dateTime('completata_il')->nullable();
            $table->foreignId('completata_da')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('ordine')->default(0);
            $table->timestamps();
        });

        Schema::create('timer_intervento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('intervento_id')->constrained('interventi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('inizio');
            $table->dateTime('fine')->nullable();
            $table->integer('minuti')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('timer_intervento');
        Schema::dropIfExists('intervento_checklist');
        Schema::dropIfExists('intervento_foto');
        Schema::dropIfExists('intervento_articoli');
        Schema::dropIfExists('interventi');
    }
};
