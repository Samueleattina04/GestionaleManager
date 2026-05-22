<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventoChecklist extends Model
{
    protected $table = 'intervento_checklist';
    protected $fillable = ['tenant_id', 'intervento_id', 'voce', 'completata', 'completata_il', 'completata_da', 'ordine'];
    protected $casts = ['completata' => 'boolean', 'completata_il' => 'datetime'];

    public function intervento(): BelongsTo { return $this->belongsTo(Intervento::class); }
    public function completataDa(): BelongsTo { return $this->belongsTo(User::class, 'completata_da'); }
}
