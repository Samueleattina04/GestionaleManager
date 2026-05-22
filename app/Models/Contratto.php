<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contratto extends Model
{
    use SoftDeletes;

    protected $table = 'contratti';

    protected $fillable = [
        'tenant_id', 'numero', 'cliente_id', 'titolo', 'tipo', 'stato',
        'data_inizio', 'data_fine', 'importo', 'frequenza_fatturazione',
        'frequenza_manutenzione', 'descrizione', 'note', 'rinnovo_automatico',
    ];

    protected $casts = [
        'data_inizio' => 'date', 'data_fine' => 'date',
        'importo' => 'decimal:2', 'rinnovo_automatico' => 'boolean',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function manutenzioni(): HasMany { return $this->hasMany(ManutenzoneProgrammata::class); }
    public function interventi(): HasMany { return $this->hasMany(Intervento::class); }

    public function isInScadenza(int $giorni = 30): bool
    {
        return $this->data_fine->diffInDays(now()) <= $giorni && $this->data_fine->isFuture();
    }
}
