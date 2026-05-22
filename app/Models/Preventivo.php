<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Preventivo extends Model
{
    use SoftDeletes;

    protected $table = 'preventivi';

    protected $fillable = [
        'tenant_id', 'numero', 'cliente_id', 'user_id', 'data', 'data_scadenza',
        'stato', 'oggetto', 'note', 'condizioni', 'sconto_globale',
        'totale_imponibile', 'totale_iva', 'totale', 'inviato_il',
    ];

    protected $casts = [
        'data' => 'date', 'data_scadenza' => 'date', 'inviato_il' => 'datetime',
        'sconto_globale' => 'decimal:2', 'totale_imponibile' => 'decimal:2',
        'totale_iva' => 'decimal:2', 'totale' => 'decimal:2',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function righe(): HasMany { return $this->hasMany(PreventivoRiga::class)->orderBy('ordine'); }

    public function getStatoLabelAttribute(): string
    {
        return match($this->stato) {
            'bozza' => 'Bozza', 'inviato' => 'Inviato', 'accettato' => 'Accettato',
            'rifiutato' => 'Rifiutato', 'scaduto' => 'Scaduto', default => $this->stato,
        };
    }
}
