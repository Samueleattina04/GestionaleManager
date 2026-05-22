<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventoArticolo extends Model
{
    protected $table = 'intervento_articoli';
    protected $fillable = ['tenant_id', 'intervento_id', 'articolo_id', 'magazzino_id', 'quantita', 'prezzo_unitario', 'sconto'];
    protected $casts = ['quantita' => 'decimal:4', 'prezzo_unitario' => 'decimal:4', 'sconto' => 'decimal:2'];

    public function intervento(): BelongsTo { return $this->belongsTo(Intervento::class); }
    public function articolo(): BelongsTo { return $this->belongsTo(Articolo::class); }
    public function magazzino(): BelongsTo { return $this->belongsTo(Magazzino::class); }
}
