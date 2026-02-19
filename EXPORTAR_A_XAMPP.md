# Exportar / clonar la base de datos a XAMPP

Este proyecto puede usar **SQLite** (por defecto) o **MySQL** (recomendado en XAMPP). Para clonar tu base de datos actual (SQLite) a MySQL en XAMPP, sigue estos pasos.

## Requisitos

- XAMPP instalado con **MySQL** y **Apache** en ejecución.
- En el panel de XAMPP, asegúrate de que MySQL esté **Started**.

## Pasos

### 1. Crear la base de datos en MySQL (XAMPP)

1. Abre **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Clic en **Nueva** (o **New**) para crear una base de datos.
3. Nombre sugerido: `crm_nv` (o el que prefieras).
4. Cotejamiento: `utf8mb4_unicode_ci`.
5. Clic en **Crear**.

### 2. Configurar Laravel para MySQL

Edita el archivo `.env` en la raíz del proyecto y deja la base de datos así (ajusta nombre y contraseña si los cambiaste en MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_nv
DB_USERNAME=root
DB_PASSWORD=
```

Si tienes contraseña en `root` de MySQL, ponla en `DB_PASSWORD=`.

### 3. Crear las tablas en MySQL

Desde la carpeta del proyecto (donde está `artisan`), en la terminal:

```bash
php artisan migrate --force
```

Con esto se crean todas las tablas en la base MySQL (vacías).

### 4. Clonar los datos desde SQLite a MySQL

Si ya tenías datos en la base SQLite (`database/database.sqlite`) y quieres copiarlos a MySQL:

1. **Vuelve a poner temporalmente SQLite en el .env** (para que el comando lea desde el archivo SQLite):

   ```env
   DB_CONNECTION=sqlite
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=crm_nv
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

2. Crea la base de datos en MySQL y las tablas (si no lo hiciste en el paso 3), pero **usando MySQL**. La forma más simple es:
   - Dejar en `.env`: `DB_CONNECTION=mysql` y los datos de MySQL (paso 2).
   - Ejecutar: `php artisan migrate --force`.
   - Luego ejecutar el comando de clonado; el comando usa una conexión interna al archivo SQLite.

3. Ejecuta el comando de clonado (usa la conexión MySQL del `.env` y lee desde `database/database.sqlite` por defecto):

   ```bash
   php artisan db:clone-to-mysql
   ```

   Para que el comando **cree las tablas en MySQL y luego copie los datos** en un solo paso (si la base MySQL está vacía):

   ```bash
   php artisan db:clone-to-mysql --fresh
   ```

4. Vuelve a dejar en `.env` la conexión MySQL para usar la app en XAMPP:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crm_nv
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 5. Probar la aplicación

- Asegúrate de que Apache y MySQL estén iniciados en XAMPP.
- Abre en el navegador: `http://localhost/CRMnv/public` (o la URL que uses para este proyecto).

---

## Resumen rápido (solo estructura en XAMPP, sin datos)

1. En phpMyAdmin crea la base de datos `crm_nv`.
2. En `.env` pon `DB_CONNECTION=mysql`, `DB_DATABASE=crm_nv`, `DB_USERNAME=root`, `DB_PASSWORD=`.
3. Ejecuta: `php artisan migrate --force`.

## Resumen rápido (clonar datos de SQLite a MySQL en XAMPP)

1. Crea la base `crm_nv` en phpMyAdmin.
2. En `.env` configura MySQL (`DB_CONNECTION=mysql`, etc.).
3. Ejecuta: `php artisan db:clone-to-mysql --fresh` (crea tablas y copia datos desde `database/database.sqlite`).

Si las tablas ya existen en MySQL, usa solo:

```bash
php artisan db:clone-to-mysql
```
