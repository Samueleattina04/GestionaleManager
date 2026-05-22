<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentoMagazzino extends Model
{
    protected $table = 'movimenti_magazzino';
    protected $fillable = [
        'tenant_id', 'articolo_id', 'magazzino_id', 'magazzino_destinazione_id',
        'user_id', 'intervento_id', 'tipo', 'quantita', 'prezzo_unitario', 'causale', 'note',
    ];
    protected $casts = ['quantita' => 'decimal:4', 'prezzo_unitario' => 'decimal:4'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function articolo(): BelongsTo { return $this->belongsTo(Articolo::class); }
    public function magazzino(): BelongsTo { return $this->belongsTo(Magazzino::class); }
    public function destinazione(): BelongsTo { return $this->belongsTo(Magazzino::class, 'magazzino_destinazione_id'); }
    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
