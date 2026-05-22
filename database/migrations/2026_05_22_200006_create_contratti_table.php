<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contratti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('numero', 20);
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->string('titolo');
            $table->enum('tipo', ['assistenza', 'manutenzione', 'noleggio', 'altro'])->default('assistenza');
            $table->enum('stato', ['attivo', 'scaduto', 'sospeso', 'annullato'])->default('attivo');
            $table->date('data_inizio');
            $table->date('data_fine');
            $table->decimal('importo', 12, 2)->default(0);
            $table->enum('frequenza_fatturazione', ['mensile', 'trimestrale', 'semestrale', 'annuale'])->default('annuale');
            $table->enum('frequenza_manutenzione', ['settimanale', 'mensile', 'bimestrale', 'trimestrale', 'semestrale', 'annuale'])->nullable();
            $table->text('descrizione')->nullable();
            $table->text('note')->nullable();
            $table->boolean('rinnovo_automatico')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'stato']);
        });

        Schema::create('manutenzioni_programmate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('contratto_id')->constrained('contratti')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->foreignId('intervento_id')->nullable()->constrained('interventi')->nullOnDelete();
            $table->date('data_pianificata');
            $table->enum('stato', ['programmata', 'completata', 'saltata'])->default('programmata');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('manutenzioni_programmate');
        Schema::dropIfExists('contratti');
    }
};
