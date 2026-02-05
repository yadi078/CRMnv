# Entender el error 403 (Forbidden)

## ¿Qué significa?

**403 Forbidden** = el servidor entendió la petición pero **no tiene permiso** para devolver ese recurso. No es que la página no exista (eso sería 404), sino que el acceso está **prohibido**.

---

## Cómo saber QUÉ está devolviendo 403

1. Abre **DevTools** en el navegador (F12).
2. Ve a la pestaña **Network** (Red).
3. Recarga la página (F5).
4. En la lista de peticiones, busca las que aparecen en **rojo** con estado **403**.
5. Haz clic en esa petición y revisa:
   - **Request URL**: qué URL está devolviendo 403 (página, CSS, JS, imagen, etc.).
   - **Headers** o **Preview**: a veces Laravel devuelve HTML con el mensaje "Esta acción no está autorizada".

Con eso sabes si el 403 es de una **ruta de la app** (Laravel) o de un **recurso estático** (archivo, Vite, etc.).

---

## Causas habituales en este proyecto

### 1. Laravel: falta de permiso (política / rol)

- **Síntoma:** Al entrar a una URL (ej. `/companies`, `/contacts`) ves 403 y el mensaje *"Esta acción no está autorizada"*.
- **Motivo:** El usuario no tiene el **rol** o **permiso** necesario (ej. `companies.view`).
- **Qué hacer:**
  - Asegúrate de haber ejecutado los seeders de roles y permisos:
    ```bash
    php artisan db:seed --class=RolePermissionSeeder
    ```
  - El middleware `ensure.role` asigna el rol `usuario` a usuarios sin rol; si el rol no existe en la BD, no se asigna y seguirás con 403.
  - Si el usuario ya existía antes de los seeders, cierra sesión y vuelve a entrar para que se le asigne el rol en la siguiente petición.

### 2. Recursos Vite (JS/CSS) devuelven 403

- **Síntoma:** En Network ves 403 en URLs como `http://localhost:5173/@vite/...` o algo bajo `/build/` o `/resources/`.
- **Motivo:** El servidor de Vite no está corriendo, o Apache está sirviendo una ruta que no debe (o bloqueando por configuración).
- **Qué hacer:**
  - Si usas **Vite en desarrollo**, en la raíz del proyecto ejecuta:
    ```bash
    npm run dev
    ```
  - Si no usas Vite en dev, genera la build y usa la app con la carpeta `public`:
    ```bash
    npm run build
    ```
  - Las vistas usan `@vite(...)`; si no hay `public/build` ni servidor de Vite, algunas peticiones de recursos pueden fallar (404 o 403 según el servidor).

### 3. Apache / XAMPP bloquea una ruta o tipo de archivo

- **Síntoma:** 403 en una URL que apunta a un archivo (ej. `/img/...`, `/storage/...`, algo en `public/`).
- **Motivo:** Permisos de carpetas en Windows, o reglas de `.htaccess` / configuración de Apache que deniegan acceso.
- **Qué hacer:**
  - Revisa que la **URL que da 403** en Network sea la que crees (página vs. archivo).
  - Comprueba permisos de la carpeta `public` y subcarpetas (que el usuario con el que corre Apache pueda leer).
  - Revisa si hay otro `.htaccess` en carpetas padre que pueda estar denegando acceso.

### 4. Peticiones AJAX / fetch con 403

- **Síntoma:** 403 en una petición que hace tu JavaScript (por ejemplo al enviar un formulario o cargar datos).
- **Motivo:** Muy a menudo en Laravel es **CSRF**: falta el token o la ruta no acepta el token.
- **Qué hacer:**
  - Asegúrate de que en el `<head>` exista `<meta name="csrf-token" content="{{ csrf_token() }}">` y que tu JS envíe el header `X-CSRF-TOKEN` (o el token en el body) en peticiones POST/PUT/DELETE.
  - Si la ruta está protegida por `auth` o políticas, el usuario debe estar autenticado; si la sesión expiró, el servidor puede responder 403.

---

## Resumen

| Dónde ves el 403 | Causa más probable | Acción |
|------------------|--------------------|--------|
| Al abrir una página (ej. `/companies`) | Permiso/rol en Laravel | Seeders + rol `usuario` + cerrar sesión y volver a entrar |
| En un archivo JS/CSS (Vite, `/build/`) | Vite no corre o build no generada | `npm run dev` o `npm run build` |
| En una ruta de API / formulario enviado por JS | CSRF o sesión/permiso | Revisar token CSRF y que el usuario esté logueado |

Siempre revisa la **URL exacta** que devuelve 403 en la pestaña Network para saber si es una ruta Laravel o un recurso estático.
