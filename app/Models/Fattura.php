<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fattura extends Model
{
    use SoftDeletes;

    protected $table = 'fatture';

    protected $fillable = [
        'tenant_id', 'numero', 'anno', 'tipo', 'cliente_id', 'fornitore_id', 'preventivo_id',
        'data', 'data_scadenza', 'stato', 'modalita_pagamento', 'note',
        'sconto_globale', 'totale_imponibile', 'totale_iva', 'totale', 'pagato',
        'sdi_id', 'sdi_stato',
    ];

    protected $casts = [
        'data' => 'date', 'data_scadenza' => 'date',
        'sconto_globale' => 'decimal:2', 'totale_imponibile' => 'decimal:2',
        'totale_iva' => 'decimal:2', 'totale' => 'decimal:2', 'pagato' => 'decimal:2',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function fornitore(): BelongsTo { return $this->belongsTo(Fornitore::class); }
    public function preventivo(): BelongsTo { return $this->belongsTo(Preventivo::class); }
    public function righe(): HasMany { return $this->hasMany(FatturaRiga::class)->orderBy('ordine'); }
    public function pagamenti(): HasMany { return $this->hasMany(Pagamento::class); }

    public function getSaldoAttribute(): float { return $this->totale - $this->pagato; }

    public function getStatoLabelAttribute(): string
    {
        return match($this->stato) {
            'bozza' => 'Bozza', 'emessa' => 'Emessa',
            'pagata_parzialmente' => 'Pag. Parziale', 'pagata' => 'Pagata',
            'scaduta' => 'Scaduta', 'annullata' => 'Annullata', default => $this->stato,
        };
    }
}
