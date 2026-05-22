<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpostazioneTenant extends Model
{
    protected $table = 'impostazioni_tenant';
    protected $fillable = ['tenant_id', 'chiave', 'valore', 'tipo'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
}
