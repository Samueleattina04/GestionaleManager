<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifica extends Model
{
    protected $table = 'notifiche';
    protected $fillable = ['tenant_id', 'user_id', 'tipo', 'titolo', 'messaggio', 'dati', 'url', 'letta', 'letta_il'];
    protected $casts = ['dati' => 'array', 'letta' => 'boolean', 'letta_il' => 'datetime'];

    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }

    public function segnaLetta(): void
    {
        $this->update(['letta' => true, 'letta_il' => now()]);
    }
}
