<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifiche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 50);
            $table->string('titolo');
            $table->text('messaggio');
            $table->json('dati')->nullable();
            $table->string('url')->nullable();
            $table->boolean('letta')->default(false);
            $table->dateTime('letta_il')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'user_id', 'letta']);
        });

        Schema::create('preferenze_notifiche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo_evento', 50);
            $table->boolean('email')->default(true);
            $table->boolean('push')->default(true);
            $table->boolean('in_app')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'tipo_evento']);
        });

        Schema::create('posizioni_gps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('latitudine', 10, 8);
            $table->decimal('longitudine', 11, 8);
            $table->decimal('precisione', 8, 2)->nullable();
            $table->dateTime('rilevato_il');
            $table->timestamps();
            $table->index(['tenant_id', 'user_id', 'rilevato_il']);
        });

        Schema::create('stati_tecnici', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('disponibile')->default(false);
            $table->dateTime('inizio_turno')->nullable();
            $table->dateTime('fine_turno')->nullable();
            $table->decimal('ultima_lat', 10, 8)->nullable();
            $table->decimal('ultima_lng', 11, 8)->nullable();
            $table->dateTime('ultima_posizione_il')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('log_attivita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('azione', 50);
            $table->string('modello', 50)->nullable();
            $table->unsignedBigInteger('modello_id')->nullable();
            $table->json('dati_vecchi')->nullable();
            $table->json('dati_nuovi')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('impostazioni_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('chiave', 100);
            $table->text('valore')->nullable();
            $table->string('tipo', 20)->default('stringa');
            $table->timestamps();
            $table->unique(['tenant_id', 'chiave']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('impostazioni_tenant');
        Schema::dropIfExists('log_attivita');
        Schema::dropIfExists('stati_tecnici');
        Schema::dropIfExists('posizioni_gps');
        Schema::dropIfExists('preferenze_notifiche');
        Schema::dropIfExists('notifiche');
    }
};
