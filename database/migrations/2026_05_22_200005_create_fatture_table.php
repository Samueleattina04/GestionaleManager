<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fatture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('numero', 20);
            $table->integer('anno');
            $table->enum('tipo', ['fattura', 'nota_credito', 'ddt', 'fattura_acquisto'])->default('fattura');
            $table->foreignId('cliente_id')->nullable()->constrained('clienti')->nullOnDelete();
            $table->foreignId('fornitore_id')->nullable()->constrained('fornitori')->nullOnDelete();
            $table->foreignId('preventivo_id')->nullable()->constrained('preventivi')->nullOnDelete();
            $table->date('data');
            $table->date('data_scadenza')->nullable();
            $table->enum('stato', ['bozza', 'emessa', 'pagata_parzialmente', 'pagata', 'scaduta', 'annullata'])->default('bozza');
            $table->string('modalita_pagamento', 50)->nullable();
            $table->text('note')->nullable();
            $table->decimal('sconto_globale', 5, 2)->default(0);
            $table->decimal('totale_imponibile', 12, 2)->default(0);
            $table->decimal('totale_iva', 12, 2)->default(0);
            $table->decimal('totale', 12, 2)->default(0);
            $table->decimal('pagato', 12, 2)->default(0);
            $table->string('sdi_id')->nullable();
            $table->enum('sdi_stato', ['non_inviata', 'in_attesa', 'consegnata', 'rifiutata', 'accettata'])->default('non_inviata');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'stato', 'anno']);
            $table->unique(['tenant_id', 'numero', 'anno', 'tipo']);
        });

        Schema::create('fattura_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('fattura_id')->constrained('fatture')->cascadeOnDelete();
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

        Schema::create('pagamenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('fattura_id')->constrained('fatture')->cascadeOnDelete();
            $table->decimal('importo', 12, 2);
            $table->date('data_pagamento');
            $table->string('metodo', 50)->nullable();
            $table->string('riferimento', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('registrato_da')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pagamenti');
        Schema::dropIfExists('fattura_righe');
        Schema::dropIfExists('fatture');
    }
};
