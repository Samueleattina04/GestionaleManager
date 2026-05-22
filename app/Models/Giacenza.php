<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Giacenza extends Model
{
    protected $fillable = ['tenant_id', 'articolo_id', 'magazzino_id', 'quantita'];
    protected $casts = ['quantita' => 'decimal:4'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function articolo(): BelongsTo { return $this->belongsTo(Articolo::class); }
    public function magazzino(): BelongsTo { return $this->belongsTo(Magazzino::class); }
}
