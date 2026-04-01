<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use App\Models\Contact;
use App\Models\Reminder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

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
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * URL de la foto de perfil (para usar en img src).
     *
     * - En el navegador usamos ruta relativa (/CRMnv/public/storage/...) para XAMPP en subcarpeta
     *   sin depender de APP_URL ni del host.
     * - Fuera de la petición HTTP (correos, consola): URL absoluta con app.url.
     *
     * Requiere: php artisan storage:link (public/storage → storage/app/public).
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        $relative = str_replace('\\', '/', ltrim($this->profile_photo_path, '/'));

        $baseUrl = (! app()->runningInConsole() && request())
            ? (rtrim(request()->getBasePath(), '/')).'/storage/'.$relative
            : rtrim((string) config('app.url'), '/').'/storage/'.$relative;

        // Evita que el navegador muestre la imagen en caché de otra sesión o usuario (misma ruta, distinto dueño).
        $cacheKey = (string) ($this->updated_at?->getTimestamp() ?? 0);
        $sep = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl.$sep.'u='.$this->getKey().'&v='.$cacheKey;
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
     * Empresas donde este usuario figura como ejecutivo asignado (admin).
     */
    public function assignedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'assigned_user_id');
    }

    /**
     * Contactos asignados directamente a este ejecutivo.
     */
    public function assignedContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'assigned_user_id');
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
     * Relación: Recordatorios personales del usuario
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    /** Cantidad de notificaciones no leídas (incluye recordatorios; mismo criterio que la vista /notifications). */
    public function unreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
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
     * Administradores que deben recibir avisos del sistema (nuevo contacto, registro, etc.).
     * Incluye rol "admin" y "administrador" (misma lógica que esAdmin()).
     */
    public static function administradoresParaNotificaciones(): Collection
    {
        $guard = config('auth.defaults.guard') ?? 'web';
        $nombres = ['admin', 'administrador'];
        $existentes = array_values(array_filter($nombres, function (string $nombre) use ($guard): bool {
            return Role::query()
                ->where('name', $nombre)
                ->where('guard_name', $guard)
                ->exists();
        }));

        if ($existentes === []) {
            return collect();
        }

        return static::query()
            ->role($existentes)
            ->get();
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
