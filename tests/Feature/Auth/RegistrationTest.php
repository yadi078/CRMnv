<?php

namespace Tests\Feature\Auth;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // Aseguramos que exista el rol requerido por el registro
        Role::firstOrCreate(
            ['name' => 'usuario', 'guard_name' => 'web'],
            ['name' => 'usuario', 'guard_name' => 'web']
        );

        // También el rol admin usado por los listeners/notificaciones
        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // El usuario se crea pero queda pendiente de aprobación, no autenticado
        $this->assertGuest();
        $response->assertRedirect(route('register.pending', absolute: false));
    }
}
