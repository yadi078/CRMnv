<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'company_rfc',
        'company_id',
        'email',
        'password',
        'profile_photo_path',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    /**
     * Atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben convertirse.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * URL de la foto de perfil (para usar en img src).
     * Si no hay foto, devuelve null para mostrar iniciales o placeholder.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo_path) {
            return null;
        }
        return Storage::url($this->profile_photo_path);
    }

    /**
     * Iniciales del nombre (para avatar cuando no hay foto).
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/', $name, 2);
        $first = mb_substr($parts[0], 0, 1, 'UTF-8');
        $last = isset($parts[1]) ? mb_substr($parts[1], 0, 1, 'UTF-8') : '';
        return mb_strtoupper($first . $last, 'UTF-8');
    }

    /**
     * Relación: Usuario que aprobó a este usuario
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relación: Empresa a la que pertenece el usuario
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Empresas creadas por este usuario
     */
    public function companiesCreated(): HasMany
    {
        return $this->hasMany(Company::class, 'created_by');
    }

    /**
     * Relación: Empresas aprobadas por este usuario
     */
    public function companiesApproved(): HasMany
    {
        return $this->hasMany(Company::class, 'approved_by');
    }

    /**
     * Verifica si el usuario es administrador.
     * Acepta los nombres de rol 'admin' y 'administrador' por compatibilidad.
     */
    public function esAdmin(): bool
    {
        return $this->hasRole(['admin', 'administrador']);
    }

    /**
     * Verifica si el usuario está aprobado
     */
    public function estaAprobado(): bool
    {
        return $this->approval_status === 'aprobado';
    }

    /**
     * Aprobar usuario
     */
    public function aprobar(int $adminId): void
    {
        $this->update([
            'approval_status' => 'aprobado',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Denegar solicitud de registro del usuario
     */
    public function denegar(int $adminId, ?string $motivo = null): void
    {
        $this->update([
            'approval_status' => 'rechazado',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }
}
