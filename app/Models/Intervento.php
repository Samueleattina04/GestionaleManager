<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intervento extends Model
{
    use SoftDeletes;

    protected $table = 'interventi';

    protected $fillable = [
        'tenant_id', 'numero', 'cliente_id', 'tecnico_id', 'contratto_id',
        'titolo', 'descrizione', 'priorita', 'stato',
        'data_pianificata', 'data_inizio_effettivo', 'data_fine_effettivo',
        'minuti_lavorati', 'note_interne', 'note_cliente', 'firma_cliente',
        'indirizzo_intervento', 'latitudine', 'longitudine',
        'fatturato', 'fattura_id',
    ];

    protected $casts = [
        'data_pianificata' => 'datetime',
        'data_inizio_effettivo' => 'datetime',
        'data_fine_effettivo' => 'datetime',
        'fatturato' => 'boolean',
        'latitudine' => 'decimal:8',
        'longitudine' => 'decimal:8',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function tecnico(): BelongsTo { return $this->belongsTo(User::class, 'tecnico_id'); }
    public function contratto(): BelongsTo { return $this->belongsTo(Contratto::class); }
    public function fattura(): BelongsTo { return $this->belongsTo(Fattura::class); }
    public function articoli(): HasMany { return $this->hasMany(InterventoArticolo::class); }
    public function foto(): HasMany { return $this->hasMany(InterventoFoto::class); }
    public function checklist(): HasMany { return $this->hasMany(InterventoChecklist::class)->orderBy('ordine'); }
    public function timers(): HasMany { return $this->hasMany(TimerIntervento::class); }

    public function getPrioritaLabelAttribute(): string
    {
        return match($this->priorita) {
            'urgente' => 'Urgente',
            'normale' => 'Normale',
            'bassa' => 'Bassa',
            default => $this->priorita,
        };
    }

    public function getStatoLabelAttribute(): string
    {
        return match($this->stato) {
            'da_assegnare' => 'Da assegnare',
            'assegnato' => 'Assegnato',
            'in_corso' => 'In corso',
            'completato' => 'Completato',
            'annullato' => 'Annullato',
            default => $this->stato,
        };
    }

    public function getStatoColorAttribute(): string
    {
        return match($this->stato) {
            'da_assegnare' => 'warning',
            'assegnato' => 'info',
            'in_corso' => 'primary',
            'completato' => 'success',
            'annullato' => 'secondary',
            default => 'secondary',
        };
    }
}
