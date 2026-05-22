<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Magazzino extends Model
{
    use SoftDeletes;

    protected $table = 'magazzini';

    protected $fillable = [
        'tenant_id', 'nome', 'descrizione', 'indirizzo',
        'principale', 'mobile', 'responsabile_id', 'attivo',
    ];

    protected $casts = [
        'principale' => 'boolean',
        'mobile' => 'boolean',
        'attivo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function responsabile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsabile_id');
    }

    public function giacenze(): HasMany
    {
        return $this->hasMany(Giacenza::class);
    }

    public function movimenti(): HasMany
    {
        return $this->hasMany(MovimentoMagazzino::class);
    }
}
