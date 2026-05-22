<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome', 'slug', 'ragione_sociale', 'partita_iva', 'codice_fiscale',
        'indirizzo', 'citta', 'provincia', 'cap', 'paese',
        'telefono', 'email', 'pec', 'codice_sdi',
        'logo', 'colore_primario', 'colore_secondario',
        'attivo', 'piano', 'scadenza_abbonamento', 'importo_abbonamento', 'impostazioni',
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'scadenza_abbonamento' => 'date',
        'importo_abbonamento' => 'decimal:2',
        'impostazioni' => 'array',
    ];

    public function utenti(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function clienti(): HasMany
    {
        return $this->hasMany(Cliente::class, 'tenant_id');
    }

    public function fornitori(): HasMany
    {
        return $this->hasMany(Fornitore::class, 'tenant_id');
    }

    public function articoli(): HasMany
    {
        return $this->hasMany(Articolo::class, 'tenant_id');
    }

    public function magazzini(): HasMany
    {
        return $this->hasMany(Magazzino::class, 'tenant_id');
    }

    public function interventi(): HasMany
    {
        return $this->hasMany(Intervento::class, 'tenant_id');
    }

    public function fatture(): HasMany
    {
        return $this->hasMany(Fattura::class, 'tenant_id');
    }

    public function preventivi(): HasMany
    {
        return $this->hasMany(Preventivo::class, 'tenant_id');
    }

    public function contratti(): HasMany
    {
        return $this->hasMany(Contratto::class, 'tenant_id');
    }

    public function impostazioni(): HasMany
    {
        return $this->hasMany(ImpostazioneTenant::class, 'tenant_id');
    }

    public function getImpostazione(string $chiave, mixed $default = null): mixed
    {
        $impostazione = $this->impostazioni()->where('chiave', $chiave)->first();
        return $impostazione ? $impostazione->valore : $default;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
