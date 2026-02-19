# Si ves "Error Interno" o "No existe el archivo o directorio" (Controll)

Cuando al abrir **Gestión de Datos** o otra ruta aparece un error que menciona `Controll` o que no encuentra un controlador, suele deberse a caché o autoload desactualizado.

Ejecuta en la raíz del proyecto (donde está `artisan`), en orden:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

Si usas XAMPP, abre **Símbolo del sistema** o **PowerShell**, ve a la carpeta del proyecto (`cd C:\xampp\htdocs\CRMnv`) y ejecuta los comandos. Si `composer` no se reconoce, usa la ruta completa de PHP/Composer o ejecuta solo los cuatro primeros (artisan).

Después de crear la tabla de notificaciones, ejecuta también:

```bash
php artisan migrate
```
