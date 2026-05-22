<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'telefono',
        'avatar', 'is_super_admin', 'attivo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
        'attivo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function interventiAssegnati(): HasMany
    {
        return $this->hasMany(Intervento::class, 'tecnico_id');
    }

    public function notifiche(): HasMany
    {
        return $this->hasMany(Notifica::class);
    }

    public function statoTecnico(): HasOne
    {
        return $this->hasOne(StatoTecnico::class);
    }

    public function posizioniGps(): HasMany
    {
        return $this->hasMany(PosizioneGps::class);
    }

    public function isTitolare(): bool
    {
        return $this->hasRole('titolare');
    }

    public function isTecnico(): bool
    {
        return $this->hasRole('tecnico');
    }

    public function isAmministratore(): bool
    {
        return $this->hasRole('amministratore');
    }

    public function isMagazziniere(): bool
    {
        return $this->hasRole('magazziniere');
    }

    public function isCommerciale(): bool
    {
        return $this->hasRole('commerciale');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function notificheNonLette(): int
    {
        return $this->notifiche()->where('letta', false)->count();
    }
}
