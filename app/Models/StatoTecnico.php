<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatoTecnico extends Model
{
    protected $table = 'stati_tecnici';
    protected $fillable = ['tenant_id', 'user_id', 'disponibile', 'inizio_turno', 'fine_turno', 'ultima_lat', 'ultima_lng', 'ultima_posizione_il'];
    protected $casts = ['disponibile' => 'boolean', 'inizio_turno' => 'datetime', 'fine_turno' => 'datetime', 'ultima_posizione_il' => 'datetime'];

    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
