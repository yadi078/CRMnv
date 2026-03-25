# Notificaciones en el CRM

## Quién recibe avisos en el panel (campana)

- **Nuevo contacto:** usuarios con rol Spatie **`admin`** o **`administrador`** (misma regla que `User::esAdmin()`).
- **Nuevo registro de usuario:** los mismos administradores.
- **Cumpleaños / seguimientos:** ya filtraban ambos roles o equivalente.

Si no hay nadie con esos roles, no se guardan filas en la tabla `notifications` para esos eventos. Ejecute los seeders o asigne el rol en la base de datos.

## Correo electrónico

- **Aprobación de usuario** (`UserApprovedNotification`) usa los canales `mail` y `database`.
- Con `MAIL_MAILER=log` (valor por defecto si no configura `.env`), **no llega correo a buzones**; solo queda registro en `storage/logs/laravel.log`.
- Para correo real, configure `MAIL_MAILER=smtp` (u otro driver) y host, usuario y contraseña en `.env`.

## Si “dejaron de llegar”

1. Compruebe que exista al menos un usuario con rol **`admin`** o **`administrador`**.
2. Revise `storage/logs/laravel.log` por errores al notificar.
3. Para SMTP, verifique `MAIL_*` y que el servidor no bloquee el envío.
4. Recordatorios programados requieren **cron**: `* * * * * php /ruta/artisan schedule:run`.

## Solicitud de eliminación (empresa / contacto)

- Si un administrador **aprueba** la baja, el usuario que la solicitó recibe una notificación en el panel («Eliminación aprobada»).
- Si **deniega** la baja, debe escribir un **motivo obligatorio**; el usuario recibe notificación y además ve un **aviso en la ficha** del empresa o contacto con ese texto.

## Recordatorios personales (panel)

- El comando `reminders:send-due` corre **cada minuto** (scheduler).
- Cada recordatorio con hora genera **dos** notificaciones en base de datos:
  1. **En 10 minutos** (`alert_phase`: `pre`): en el intervalo entre 10 minutos antes y la hora programada.
  2. **En la hora** (`alert_phase`: `due`): cuando llega o pasa la hora de `start_at` (si no se había enviado ya).
- En la vista **Notificaciones**, el sonido del navegador usa un tono más suave para el aviso previo y el doble pitido para el de la hora.
- Si cambias fecha/hora del recordatorio, se reinician los campos de envío para que vuelvan a dispararse.

## Cumpleaños de contactos

- Se considera **el mismo día calendario** en la zona **`APP_TIMEZONE`** (`config('app.timezone')`): se comparan el mes y el día de `fecha_cumpleaños` con el mes y el día de “hoy” en esa zona.
- Si `APP_TIMEZONE` es `UTC` pero operas en México, el aviso podía salir **un día antes o después** respecto al calendario local. Usa por ejemplo `America/Mexico_City`.
- El comando programado es `birthdays:notify` (ver `routes/console.php`). Hora local: **`BIRTHDAY_NOTIFY_TIME`** (por defecto `08:00`), definida en `config/crm.php`.
- Sin cron, al abrir **Notificaciones** como admin se intenta una vez al día (caché) el mismo envío.
