<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Usuarios administradores iniciales (seeders)
    |--------------------------------------------------------------------------
    |
    | Valores por defecto si no existen en .env. En producción defina
    | ADMIN_EMAIL, ADMIN_PASSWORD, etc. en el .env del servidor.
    | Si la contraseña contiene $ u otros caracteres, use comillas en .env:
    | ADMIN_PASSWORD="su_clave"
    |
    */
    'admins' => [
        [
            'email' => env('ADMIN_EMAIL', 'admin@cceconsultoria.com'),
            'password' => env('ADMIN_PASSWORD', 'admin@Olivia$'),
            'name' => env('ADMIN_NAME', 'Administrador'),
        ],
        [
            'email' => env('ADMIN_EMAIL_2', 'olivia.gallardo@ceconsultoriaempresarial.com'),
            'password' => env('ADMIN_PASSWORD_2', 'G7vQ92xLpR4ZtK8'),
            'name' => env('ADMIN_NAME_2', 'Olivia Gallardo'),
        ],
    ],
];
