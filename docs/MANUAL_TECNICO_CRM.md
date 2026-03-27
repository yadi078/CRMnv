# Manual Tecnico del CRM

## 1) Objetivo
Este documento describe la composicion tecnica del CRM de CE Consultoria: tecnologias, arquitectura, modulos, seguridad y guia de operacion para mantenimiento.

## 2) Herramienta de documentacion usada
En el repositorio no se detecta una plataforma dedicada de documentacion (por ejemplo: MkDocs, Sphinx, Docusaurus o Swagger UI para manuales funcionales).

La documentacion del proyecto se maneja en archivos Markdown (`README.md` y carpeta `docs/`).

## 3) Stack tecnologico actual
- Backend: `PHP ^8.2` + `Laravel ^12.0`.
- Frontend build: `Vite`, `Tailwind CSS`, `Alpine.js`, `Axios`.
- Base de datos: `MySQL` (configurable desde `.env`).
- Autenticacion base: `Laravel Breeze`.
- Roles y permisos: `spatie/laravel-permission`.
- Reportes/formatos: `barryvdh/laravel-dompdf` y exportacion Excel con `phpoffice/phpspreadsheet`.

## 4) Arquitectura
Arquitectura principal: MVC de Laravel, con separacion por capas:

- `app/Http/Controllers`: logica de casos de uso por modulo.
- `app/Models`: entidades de negocio (Eloquent ORM).
- `resources/views`: vistas Blade por modulo.
- `routes/web.php`: enrutamiento web y control de acceso por middleware.
- `database/migrations`: versionado de estructura de base de datos.
- `database/seeders`: datos iniciales de roles, permisos y usuarios base.

Patron de seguridad:
- Middleware `auth` y `verified`.
- Middleware `ensure.role` para garantizar que usuarios autenticados tengan rol valido.
- Middleware `admin` para funciones exclusivas de administracion.

## 5) Modulos funcionales principales
- Dashboard admin (`dashboard`) y dashboard usuario (`user.dashboard`).
- Empresas (`companies.*`): CRUD, importacion, deteccion de duplicados, solicitud de eliminacion.
- Contactos (`contacts.*`): CRUD, estado de correo, notas, solicitud de eliminacion, ficha PDF/Word (admin).
- Seguimientos (`follow-ups.*`): programacion de acciones, bitacora, marcado como completado.
- Historial de ventas (`user.sales.*`): registro, edicion, consulta y salida en PDF/Word.
- Aprobaciones (`approvals.*`, admin): aprobacion/rechazo de altas y bajas.
- Notificaciones y recordatorios (`notifications.*`, `reminders.*`).
- Gestion de datos (`data-management.*`): consulta global; export/import exclusivo admin.
- Filtros (`filtros.*`): consulta dinamica.

## 6) Control de acceso (RBAC)
El sistema utiliza `spatie/laravel-permission` con seeders:

- Roles detectados:
  - `admin`
  - `administrador` (alias equivalente)
  - `usuario`

- Permisos por dominio:
  - Empresas: ver, crear, editar, eliminar, aprobar, exportar, importar.
  - Contactos: ver, crear, editar, eliminar, exportar, generar PDF.
  - Seguimientos: ver, crear, editar, eliminar.
  - Ventas: ver, crear, editar, eliminar.
  - Usuarios: ver, crear, editar, eliminar, aprobar.
  - Dashboard: ver.

## 7) Modelo de datos (alto nivel)
Modelos principales identificados:
- `User`
- `Company`
- `Contact`
- `FollowUp`
- `Reminder`
- `Sale`
- `SaleParticipant`
- `SavedFilter`

Relaciones de negocio (resumen):
- Una empresa puede tener varios contactos.
- Contactos/empresas pueden vincularse a seguimientos.
- Una venta puede asociarse a empresa/contacto y multiples participantes.
- Usuarios generan y atienden informacion segun su rol/permisos.

## 8) Flujo de aprobaciones
El CRM incluye aprobacion administrativa para altas/bajas sensibles:
- Usuarios o capturistas generan registros.
- El admin revisa en vistas de aprobaciones.
- Puede aprobar o rechazar empresas, contactos y usuarios.
- Existen rutas de aprobacion/rechazo y de aprobacion/rechazo de solicitudes de eliminacion.

## 9) Estructura de vistas
Vistas Blade agrupadas por modulo:
- `resources/views/companies`
- `resources/views/contacts`
- `resources/views/follow-ups`
- `resources/views/user/sales`
- `resources/views/approvals`
- `resources/views/notifications`
- `resources/views/data-management`
- `resources/views/profile`
- `resources/views/filtros`

Layouts base:
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/app-user.blade.php`

Componentes de navegacion:
- `resources/views/components/sidebar-nav.blade.php` (admin)
- `resources/views/components/sidebar-nav-user.blade.php` (usuario)

## 10) Entorno e instalacion
Flujo recomendado:
1. `composer install`
2. Crear/configurar `.env`
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `npm install`
6. `npm run build` (o `npm run dev` en desarrollo)

Script util en `composer.json`:
- `composer run dev` para servidor, cola, logs y Vite en paralelo.

## 11) Operacion y mantenimiento
- Limpieza de cache:
  - `php artisan optimize:clear`
- Pruebas:
  - `php artisan test`
- Formato de codigo:
  - `./vendor/bin/pint`
- Validar permisos/roles si hay 403 inesperados:
  - Ejecutar seeders de roles/permisos y limpiar cache de permisos.

## 12) Consideraciones de seguridad
- Nunca versionar `.env` ni llaves.
- Cambiar credenciales por defecto en ambientes reales.
- Restringir rutas admin por middleware y por permisos.
- Validar cargas masivas/importaciones antes de produccion.

## 13) Pendientes recomendados para evolucion
- Documentar API interna con OpenAPI si se exponen endpoints externos.
- Añadir diagrama ER formal de base de datos.
- Incorporar manual de despliegue por ambiente (dev/staging/prod).
- Definir politica de respaldos y recuperacion.
