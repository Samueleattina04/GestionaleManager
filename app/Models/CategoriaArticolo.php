<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaArticolo extends Model
{
    protected $table = 'categorie_articoli';
    protected $fillable = ['tenant_id', 'nome', 'colore'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function articoli(): HasMany { return $this->hasMany(Articolo::class, 'categoria_id'); }
}
