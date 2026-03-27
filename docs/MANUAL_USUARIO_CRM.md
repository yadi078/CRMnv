# Manual de Usuario del CRM

## 1) Objetivo
Este manual explica que puede hacer cada perfil en el CRM, que pantallas utiliza y que datos conviene preparar para operar el sistema sin fricciones.

## 2) Perfiles del sistema
- Administrador:
  - Acceso total a modulos.
  - Puede aprobar altas/bajas.
  - Puede exportar/importar datos.
  - Puede generar documentos de contactos (PDF/Word).
- Usuario:
  - Opera captura y seguimiento comercial.
  - Gestiona empresas, contactos, seguimientos y ventas.
  - No accede a funciones exclusivas de aprobacion y administracion avanzada.

## 3) Vistas principales por perfil

### 3.1 Administrador
Menu lateral principal:
- Panel (dashboard general).
- Empresas.
- Filtros.
- Contactos.
- Seguimientos.
- Historial de ventas.
- Solicitudes pendientes (aprobaciones).
- Notificaciones.
- Gestion de Datos.
- Configuracion/Perfil.

### 3.2 Usuario
Menu lateral operativo:
- Inicio (dashboard de usuario).
- Empresas.
- Contactos.
- Seguimientos.
- Historial de ventas.
- Notificaciones.
- Perfil.

## 4) Que puede hacer cada perfil

### 4.1 Empresas
Administrador y Usuario:
- Crear, consultar, editar y listar empresas.
- Revisar duplicados.
- Solicitar eliminacion.

Administrador adicionalmente:
- Aprobar/rechazar altas o bajas pendientes.
- Ejecutar procesos de importacion/exportacion (segun permisos y vista).

### 4.2 Contactos
Administrador y Usuario:
- Crear, consultar, editar y listar contactos.
- Actualizar notas y estatus de correo.
- Solicitar eliminacion.

Administrador adicionalmente:
- Aprobar/rechazar altas o bajas pendientes.
- Generar ficha en PDF/Word.

### 4.3 Seguimientos
Administrador y Usuario:
- Crear seguimientos.
- Asignar fecha/hora de alarma.
- Registrar bitacora.
- Marcar seguimiento como completado.

### 4.4 Historial de Ventas
Administrador y Usuario:
- Registrar venta (curso/servicio).
- Relacionar empresa y opcionalmente contacto.
- Capturar monto, metodo de pago y participantes.
- Consultar detalle, editar y eliminar segun permisos.
- Descargar ficha de venta en PDF/Word.

### 4.5 Notificaciones y Recordatorios
Administrador y Usuario:
- Ver bandeja de notificaciones.
- Marcar una o varias como leidas.
- Destacar, eliminar, o limpiar notificaciones.
- Crear/editar/activar/desactivar recordatorios.

### 4.6 Aprobaciones (solo Administrador)
- Revisar solicitudes de:
  - Empresas.
  - Contactos.
  - Usuarios.
- Aprobar o rechazar desde las vistas de aprobaciones.

### 4.7 Gestion de Datos
Ambos perfiles:
- Acceso a consulta general en la vista de gestion de datos.

Solo Administrador:
- Exportar e importar informacion.
- Acciones avanzadas de administracion de datos.

## 5) Flujo operativo sugerido
1. Registrar empresa.
2. Registrar contactos asociados.
3. Programar seguimientos y alertas.
4. Registrar ventas cuando se concrete oportunidad.
5. Dar seguimiento con notificaciones y recordatorios.
6. (Admin) Aprobar pendientes de altas/bajas.

## 6) Datos que conviene preparar antes de usar el sistema

### 6.1 Para alta de empresa
- Nombre comercial (obligatorio).
- RFC (recomendado).
- Sector o giro (se permite multiple, recomendado).
- Municipio y estado.
- Ejecutivo asignado.
- Datos fiscales.

### 6.2 Para alta de contacto
- Empresa relacionada (obligatorio).
- Nombre completo (obligatorio).
- Correo electronico (obligatorio).
- Telefono/celular/ext.
- Puesto y departamento.
- Domicilio (municipio/estado).
- Datos para ficha fiscal (si aplica): razon social, calle y numero, colonia y CP, RFC, regimen.
- Notas y estatus de prospecto.

### 6.3 Para seguimiento
- Empresa y/o contacto a seguir.
- Tipo de accion (llamada, reunion, cierre).
- Fecha y hora de alarma (obligatorio).
- Responsable asignado.
- Bitacora de notas.

### 6.4 Para registro de venta
- Nombre del curso o servicio (obligatorio).
- Fecha de venta (obligatorio).
- Empresa (obligatorio).
- Contacto (opcional).
- Monto y si incluye IVA.
- Tipo de pago.
- Numero de participantes y datos de participantes.
- Datos de facturacion: colonia/CP, regimen, forma de pago, uso CFDI, orden de compra.

## 7) Buenas practicas de uso
- Validar RFC y correo antes de guardar.
- Evitar duplicados de empresa/contacto.
- Mantener notas de seguimiento claras y accionables.
- Actualizar estado de prospecto despues de cada interaccion.
- Revisar notificaciones diariamente.
- (Admin) Resolver solicitudes pendientes de aprobacion de forma periodica.

## 8) Problemas comunes
- No veo ciertas pantallas:
  - Verificar rol/permisos asignados al usuario.
- No puedo aprobar o exportar:
  - Funcion reservada para administrador.
- No llegan alertas esperadas:
  - Revisar fecha/hora del seguimiento y estado del recordatorio.

## 9) Soporte interno recomendado
Cuando un usuario reporte incidencia, solicitar:
- Correo del usuario.
- Modulo y accion exacta.
- Fecha/hora aproximada.
- Captura de pantalla del mensaje.
- Pasos para reproducir el problema.
