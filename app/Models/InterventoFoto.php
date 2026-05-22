<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventoFoto extends Model
{
    protected $table = 'intervento_foto';
    protected $fillable = ['tenant_id', 'intervento_id', 'user_id', 'percorso', 'nome_originale', 'descrizione'];

    public function intervento(): BelongsTo { return $this->belongsTo(Intervento::class); }
    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->percorso); }
}
