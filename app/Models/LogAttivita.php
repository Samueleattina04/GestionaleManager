<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAttivita extends Model
{
    protected $table = 'log_attivita';
    protected $fillable = ['tenant_id', 'user_id', 'azione', 'modello', 'modello_id', 'dati_vecchi', 'dati_nuovi', 'ip_address'];
    protected $casts = ['dati_vecchi' => 'array', 'dati_nuovi' => 'array'];
    public $timestamps = true;
    const UPDATED_AT = null;

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function utente(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
