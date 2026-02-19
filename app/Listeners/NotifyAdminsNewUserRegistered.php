<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;

class NotifyAdminsNewUserRegistered
{
    public function handle(Registered $event): void
    {
        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegisteredNotification($user));
        }
    }
}
