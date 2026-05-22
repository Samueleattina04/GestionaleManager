<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosizioneGps extends Model
{
    protected $table = 'posizioni_gps';
    protected $fillable = ['tenant_id', 'user_id', 'latitudine', 'longitudine', 'precisione', 'rilevato_il'];
    protected $casts = ['latitudine' => 'decimal:8', 'longitudine' => 'decimal:8', 'rilevato_il' => 'datetime'];

    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
