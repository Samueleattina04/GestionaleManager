<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimerIntervento extends Model
{
    protected $table = 'timer_intervento';
    protected $fillable = ['tenant_id', 'intervento_id', 'user_id', 'inizio', 'fine', 'minuti'];
    protected $casts = ['inizio' => 'datetime', 'fine' => 'datetime'];

    public function intervento(): BelongsTo { return $this->belongsTo(Intervento::class); }
    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
