<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('magazzini', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nome');
            $table->string('descrizione')->nullable();
            $table->string('indirizzo')->nullable();
            $table->boolean('principale')->default(false);
            $table->boolean('mobile')->default(false);
            $table->foreignId('responsabile_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('tenant_id');
        });

        Schema::create('categorie_articoli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nome');
            $table->string('colore', 20)->default('#6b7280');
            $table->timestamps();
        });

        Schema::create('articoli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorie_articoli')->nullOnDelete();
            $table->string('codice', 50)->nullable();
            $table->string('descrizione');
            $table->string('unita_misura', 20)->default('pz');
            $table->decimal('prezzo_acquisto', 12, 4)->default(0);
            $table->decimal('prezzo_vendita', 12, 4)->default(0);
            $table->decimal('iva', 5, 2)->default(22);
            $table->decimal('scorta_minima', 12, 4)->default(0);
            $table->text('note')->nullable();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'codice']);
        });

        Schema::create('giacenze', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('articolo_id')->constrained('articoli')->cascadeOnDelete();
            $table->foreignId('magazzino_id')->constrained('magazzini')->cascadeOnDelete();
            $table->decimal('quantita', 12, 4)->default(0);
            $table->timestamps();
            $table->unique(['articolo_id', 'magazzino_id']);
        });

        Schema::create('movimenti_magazzino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('articolo_id')->constrained('articoli')->cascadeOnDelete();
            $table->foreignId('magazzino_id')->constrained('magazzini')->cascadeOnDelete();
            $table->foreignId('magazzino_destinazione_id')->nullable()->constrained('magazzini')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('intervento_id')->nullable();
            $table->enum('tipo', ['carico', 'scarico', 'trasferimento', 'inventario', 'reso']);
            $table->decimal('quantita', 12, 4);
            $table->decimal('prezzo_unitario', 12, 4)->default(0);
            $table->string('causale')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'articolo_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('movimenti_magazzino');
        Schema::dropIfExists('giacenze');
        Schema::dropIfExists('articoli');
        Schema::dropIfExists('categorie_articoli');
        Schema::dropIfExists('magazzini');
    }
};
