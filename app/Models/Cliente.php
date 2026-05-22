<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clienti';

    protected $fillable = [
        'tenant_id', 'codice', 'tipo', 'ragione_sociale', 'nome', 'cognome',
        'partita_iva', 'codice_fiscale', 'indirizzo', 'citta', 'provincia',
        'cap', 'paese', 'latitudine', 'longitudine', 'referente',
        'telefono', 'cellulare', 'email', 'pec', 'codice_sdi',
        'note', 'campi_personalizzati', 'attivo',
    ];

    protected $casts = [
        'latitudine' => 'decimal:8',
        'longitudine' => 'decimal:8',
        'attivo' => 'boolean',
        'campi_personalizzati' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function interventi(): HasMany
    {
        return $this->hasMany(Intervento::class);
    }

    public function fatture(): HasMany
    {
        return $this->hasMany(Fattura::class);
    }

    public function preventivi(): HasMany
    {
        return $this->hasMany(Preventivo::class);
    }

    public function contratti(): HasMany
    {
        return $this->hasMany(Contratto::class);
    }

    public function getNomeCompletoAttribute(): string
    {
        if ($this->tipo === 'privato' && $this->nome && $this->cognome) {
            return "{$this->cognome} {$this->nome}";
        }
        return $this->ragione_sociale;
    }

    public function getIndirizzoCompletoAttribute(): string
    {
        $parti = array_filter([
            $this->indirizzo,
            $this->cap,
            $this->citta,
            $this->provincia ? "({$this->provincia})" : null,
        ]);
        return implode(' ', $parti);
    }
}
