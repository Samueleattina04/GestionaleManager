<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('preventivi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('numero', 20);
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('data');
            $table->date('data_scadenza')->nullable();
            $table->enum('stato', ['bozza', 'inviato', 'accettato', 'rifiutato', 'scaduto'])->default('bozza');
            $table->text('oggetto')->nullable();
            $table->text('note')->nullable();
            $table->text('condizioni')->nullable();
            $table->decimal('sconto_globale', 5, 2)->default(0);
            $table->decimal('totale_imponibile', 12, 2)->default(0);
            $table->decimal('totale_iva', 12, 2)->default(0);
            $table->decimal('totale', 12, 2)->default(0);
            $table->dateTime('inviato_il')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'stato']);
        });

        Schema::create('preventivo_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('preventivo_id')->constrained('preventivi')->cascadeOnDelete();
            $table->foreignId('articolo_id')->nullable()->constrained('articoli')->nullOnDelete();
            $table->string('descrizione');
            $table->string('unita_misura', 20)->default('pz');
            $table->decimal('quantita', 12, 4)->default(1);
            $table->decimal('prezzo_unitario', 12, 4)->default(0);
            $table->decimal('sconto', 5, 2)->default(0);
            $table->decimal('iva', 5, 2)->default(22);
            $table->decimal('totale', 12, 2)->default(0);
            $table->integer('ordine')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('preventivo_righe');
        Schema::dropIfExists('preventivi');
    }
};
