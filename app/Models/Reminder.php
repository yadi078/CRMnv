<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'extension',
        'nombre_cliente',
        'empresa',
        'correo_electronico',
        'numero_telefonico',
        'area',
        'puesto_trabajo',
        'scheduled_for',
        'start_at',
        'end_at',
        'all_day',
        'repeat',
        'deadline_at',
        'is_done',
        'notification_sent_at',
        'pre_notification_sent_at',
        'last_recurring_notify_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'all_day' => 'boolean',
        'deadline_at' => 'datetime',
        'is_done' => 'boolean',
        'notification_sent_at' => 'datetime',
        'pre_notification_sent_at' => 'datetime',
        'last_recurring_notify_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

