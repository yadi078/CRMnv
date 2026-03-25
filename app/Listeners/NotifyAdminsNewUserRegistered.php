<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyAdminsNewUserRegistered
{
    public function handle(Registered $event): void
    {
        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $admins = User::administradoresParaNotificaciones();
        if ($admins->isEmpty()) {
            Log::warning('Usuario registrado pero no hay admin/administrador para notificar.', [
                'registered_user_id' => $user->id,
            ]);

            return;
        }

        foreach ($admins as $admin) {
            try {
                $admin->notify(new NewUserRegisteredNotification($user));
            } catch (Throwable $e) {
                Log::error('No se pudo notificar a admin el registro de usuario', [
                    'admin_id' => $admin->id,
                    'registered_user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
