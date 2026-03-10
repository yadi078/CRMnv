# CRM CE Consultoría y Capacitación Empresarial

Sistema CRM desarrollado con Laravel 11, PHP 8.3, Tailwind CSS y MySQL para la gestión de empresas, contactos y seguimientos.

**Slogan:** "INVERTIR EN VALOR ¡ATRAE VALOR!"

## 🚀 Características Principales

- ✅ Gestión completa de Empresas con validación de duplicados (RFC y Nombre Comercial)
- ✅ Directorio de Contactos vinculados a empresas
- ✅ Sistema de Seguimientos con bitácora de notas y alarmas programadas
- ✅ Sistema de Semáforo visual (Verde, Amarillo, Rojo) para seguimiento de prospectos
- ✅ Sistema de Aprobaciones para registros creados por usuarios normales
- ✅ Generación automática de PDF para Fichas de Inscripción
- ✅ Control de acceso basado en roles (Admin y Usuario)
- ✅ Dashboard con resumen de actividad y seguimientos pendientes
- ✅ Interfaz moderna y responsiva con Tailwind CSS
- ✅ Paleta de colores corporativa de CE Consultoría

## 📋 Requisitos

- PHP >= 8.3
- Composer
- MySQL >= 5.7
- Node.js y NPM (para compilar assets)

## 🔧 Instalación

1. **Clonar el repositorio o navegar al directorio del proyecto**

```bash
cd CRMnv
```

2. **Instalar dependencias de PHP**

```bash
composer install
```

3. **Configurar el archivo .env**

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` y configurar la base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_cce
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

4. **Ejecutar migraciones y seeders**

```bash
php artisan migrate
php artisan db:seed
```

Esto creará:
- Tablas de base de datos
- Roles y permisos (Admin y Usuario)
- Usuario administrador: `admin@cceconsultoria.com` / `password`
- Usuario normal: `usuario@cceconsultoria.com` / `password`

5. **Instalar dependencias de Node.js (si es necesario)**

```bash
npm install
npm run build
```

6. **Iniciar el servidor de desarrollo**

```bash
php artisan serve
```

Acceder a: `http://localhost:8000`

## 👥 Usuarios por Defecto

### Administrador
- **Email:** admin@cceconsultoria.com
- **Password:** password
- **Permisos:** Acceso total, puede aprobar registros, cargar masiva, borrar registros

### Usuario Normal
- **Email:** usuario@cceconsultoria.com
- **Password:** password
- **Permisos:** Solo captura y edición. Sus registros quedan pendientes de aprobación

## 🎨 Paleta de Colores

- **Azul Fuerte:** #003366
- **Azul:** #000836 y #000099
- **Amarillo:** #FFFF00
- **Gris:** #808080

## 📊 Estructura de Base de Datos

### Tabla: companies
- Información de empresas con validación de unicidad en RFC y nombre comercial
- Sistema de semáforo (verde, amarillo, rojo)
- Sistema de aprobación (pendiente, aprobado)

### Tabla: contacts
- Contactos vinculados a empresas
- Información de contacto completa
- Generación de PDF de Ficha de Inscripción

### Tabla: follow_ups
- Seguimientos y alertas programadas
- Bitácora de notas
- Tipos: llamada, reunión, cierre

## 🔐 Permisos y Roles

### Rol: Admin
- Acceso total al sistema
- Puede aprobar empresas y usuarios
- Puede borrar registros
- Puede exportar/importar datos

### Rol: Usuario
- Solo puede crear y editar registros
- Sus empresas quedan en estado "pendiente"
- No puede borrar registros
- No puede descargar base de datos

## 📝 Funcionalidades Clave

### Validación de Duplicados
- Validación en tiempo real de RFC y Nombre Comercial
- Validación estricta de formato RFC mexicano
- Prevención de registros duplicados

### Sistema de Semáforo
- **Verde:** Última actividad hace menos de 7 días
- **Amarillo:** Última actividad hace entre 7 y 30 días
- **Rojo:** Sin actividad hace más de 30 días

### Generación de PDF
- Ficha de Inscripción automática para contactos
- Incluye información completa del contacto y empresa
- Diseño profesional con colores corporativos

## 🛠️ Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recrear base de datos
php artisan migrate:fresh --seed

# Ejecutar tests (si existen)
php artisan test
```

## 📚 Tecnologías Utilizadas

- **Laravel 11** - Framework PHP
- **PHP 8.3** - Lenguaje de programación
- **MySQL** - Base de datos
- **Tailwind CSS** - Framework CSS
- **Spatie Laravel Permission** - Gestión de roles y permisos
- **DomPDF** - Generación de PDFs
- **Laravel Breeze** - Autenticación

## 📄 Licencia

Este proyecto es propiedad de CE Consultoría y Capacitación Empresarial.

## 👨‍💻 Desarrollo

Desarrollado siguiendo las mejores prácticas de Laravel y arquitectura MVC.

---

**CE Consultoría y Capacitación Empresarial**  
*INVERTIR EN VALOR ¡ATRAE VALOR!*
