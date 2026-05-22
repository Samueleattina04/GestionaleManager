<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManutenzoneProgrammata extends Model
{
    protected $table = 'manutenzioni_programmate';
    protected $fillable = ['tenant_id', 'contratto_id', 'cliente_id', 'intervento_id', 'data_pianificata', 'stato', 'note'];
    protected $casts = ['data_pianificata' => 'date'];

    public function contratto(): BelongsTo { return $this->belongsTo(Contratto::class); }
    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function intervento(): BelongsTo { return $this->belongsTo(Intervento::class); }
}
