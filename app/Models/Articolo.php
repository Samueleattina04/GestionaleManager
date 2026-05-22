<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Articolo extends Model
{
    use SoftDeletes;

    protected $table = 'articoli';

    protected $fillable = [
        'tenant_id', 'categoria_id', 'codice', 'descrizione', 'unita_misura',
        'prezzo_acquisto', 'prezzo_vendita', 'iva', 'scorta_minima', 'note', 'attivo',
    ];

    protected $casts = [
        'prezzo_acquisto' => 'decimal:4',
        'prezzo_vendita' => 'decimal:4',
        'iva' => 'decimal:2',
        'scorta_minima' => 'decimal:4',
        'attivo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaArticolo::class, 'categoria_id');
    }

    public function giacenze(): HasMany
    {
        return $this->hasMany(Giacenza::class);
    }

    public function movimenti(): HasMany
    {
        return $this->hasMany(MovimentoMagazzino::class);
    }

    public function giacenzaMagazzino(int $magazzinoId): float
    {
        return $this->giacenze()->where('magazzino_id', $magazzinoId)->value('quantita') ?? 0;
    }

    public function giacenzaTotale(): float
    {
        return $this->giacenze()->sum('quantita');
    }

    public function isSottoScortaMinima(): bool
    {
        return $this->giacenzaTotale() <= $this->scorta_minima;
    }
}
