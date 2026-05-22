<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventivoRiga extends Model
{
    protected $table = 'preventivo_righe';
    protected $fillable = ['tenant_id', 'preventivo_id', 'articolo_id', 'descrizione', 'unita_misura', 'quantita', 'prezzo_unitario', 'sconto', 'iva', 'totale', 'ordine'];
    protected $casts = ['quantita' => 'decimal:4', 'prezzo_unitario' => 'decimal:4', 'sconto' => 'decimal:2', 'iva' => 'decimal:2', 'totale' => 'decimal:2'];

    public function preventivo(): BelongsTo { return $this->belongsTo(Preventivo::class); }
    public function articolo(): BelongsTo { return $this->belongsTo(Articolo::class); }
}
