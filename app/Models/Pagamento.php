<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    protected $table = 'pagamenti';
    protected $fillable = ['tenant_id', 'fattura_id', 'importo', 'data_pagamento', 'metodo', 'riferimento', 'note', 'registrato_da'];
    protected $casts = ['importo' => 'decimal:2', 'data_pagamento' => 'date'];

    public function fattura(): BelongsTo { return $this->belongsTo(Fattura::class); }
    public function registratoDa(): BelongsTo { return $this->belongsTo(User::class, 'registrato_da'); }
}
