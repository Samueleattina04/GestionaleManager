<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornitore extends Model
{
    use SoftDeletes;

    protected $table = 'fornitori';

    protected $fillable = [
        'tenant_id', 'codice', 'ragione_sociale', 'partita_iva', 'codice_fiscale',
        'indirizzo', 'citta', 'provincia', 'cap', 'paese',
        'referente', 'telefono', 'email', 'pec', 'codice_sdi',
        'note', 'attivo',
    ];

    protected $casts = ['attivo' => 'boolean'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function fatture(): HasMany
    {
        return $this->hasMany(Fattura::class);
    }
}
