<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Usuario administrador inicial (seeders)
    |--------------------------------------------------------------------------
    |
    | Valores por defecto si no existen en .env. En producción defina
    | ADMIN_EMAIL y ADMIN_PASSWORD en el .env del servidor.
    | Si la contraseña contiene $ u otros caracteres, use comillas en .env:
    | ADMIN_PASSWORD="su_clave"
    |
    */
    'email' => env('ADMIN_EMAIL', 'admin@cceconsultoria.com'),

    'password' => env('ADMIN_PASSWORD', 'admin@Olivia$'),
];
