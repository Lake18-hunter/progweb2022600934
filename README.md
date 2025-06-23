# Raíces del Saber – Plataforma Escolar 2025

**Raíces del Saber** es una plataforma web diseñada para la gestión escolar, permitiendo el acceso diferenciado para administradores, profesores y alumnos. La aplicación integra registro y modificación de usuarios, ingreso y consulta de calificaciones, y la opción de generar reportes en PDF. Su interfaz es moderna y permite alternar entre modo claro y oscuro, utilizando cookies para guardar dicha preferencia.

---

## Índice

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Flujo de Navegación y Roles](#flujo-de-navegación-y-roles)
- [Generación de PDFs](#generación-de-pdfs)
- [Notas Adicionales](#notas-adicionales)
- [Créditos](#créditos)
- [Licencia](#licencia)

---

## Características

- **Inicio de sesión por rol:**  
  - *Administrador:* Inicia sesión con las credenciales (ej. USERNAME: admin, Password: 1234) y es redirigido a `admin.php`  
  - *Profesor:* Luego de loguearse, se dirige a `calificaciones.php` para ingresar o modificar las calificaciones de sus alumnos y consultar el análisis en `estadísticas.php`.  
  - *Alumno:* Una vez autenticado, se redirige a `perfilalumno.php`, donde se muestran sus calificaciones y resúmenes.

- **Gestión de Usuarios:**  
  - Desde `admin.php`, el administrador puede **agregar** nuevos usuarios (profesor y alumno) y enviar la información.
  - También puede **modificar** usuarios existentes haciendo clic en “Modificar usuario”, lo que lo dirige a `modificar.php`.
  - En `resultados.php`, se visualizan todos los registros ingresados y se disponen botones para volver a agregar (ir a `admin.php`), modificar (ir a `modificar.php`) o cerrar la sesión.

- **Generación de Reportes en PDF:**  
  - Tanto en `resultados.php` (para el administrador), en `estadísticas.php` (para el profesor) y en la vista del alumno, existe un botón “Generar PDF” que abre una nueva pestaña con el reporte PDF generado mediante `generar_pdf.php`. La página original permanece intacta.

- **Interfaz y experiencia de usuario:**  
  - Alternancia entre modo claro y oscuro, implementada con cookies.
  - Notificaciones visuales con [SweetAlert2](https://sweetalert2.github.io/) para mensajes de error, confirmación y retroalimentación.

---

## Requisitos

- **Tecnologías:**  
  - PHP (con soporte para sesiones y PDO/MySQLi)
  - MySQL (o MariaDB) para la base de datos `calificaciones`
  - HTML5, CSS3, JavaScript ES6
  - Bootstrap y SweetAlert2 (incluidos vía CDN)

- **Base de Datos:**  
  La base de datos `calificaciones` debe incluir, al menos, las siguientes tablas:
  - **usuarios:** Registra las credenciales y el tipo de usuario (administrador, profesor, alumno).  
  - **alumnos:** Información personal y calificaciones (por asignatura).  
  - **profesores:** Datos del profesor, asignatura, grado, grupo, turno, etc.

---

## Instalación

1. **Descarga o clona el repositorio:**
   ```bash
   git clone https://github.com/tu_usuario/raices-del-saber.git
   ```

2. **Base de datos:**
   - Importa el esquema SQL (archivo de estructura) en tu servidor MySQL para crear la base de datos `calificaciones` y sus tablas.
   - Verifica que los nombres de campos y relaciones se correspondan con las consultas usadas en los archivos PHP.

3. **Configuración:**
   - Ajusta los parámetros de conexión (host, usuario, contraseña) en cada archivo PHP según tu entorno.

4. **Ejecución:**
   - Coloca el proyecto en tu servidor local o web.
   - Accede a `login.php` desde tu navegador para comenzar.

---

## Estructura del Proyecto

| Archivo                   | Descripción                                                                                                                                          |
|---------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------|
| **login.php**             | Página de acceso: valida credenciales (por ejemplo, USERNAME: admin / Password: 1234 para el administrador) y redirige según el rol del usuario.     |
| **admin.php**             | Interfaz del administrador para agregar nuevos usuarios (profesor y alumno). También dispone de botones para dirigirse a modificar usuarios o cerrar sesión. |
| **modificar.php**         | Panel para que el administrador modifique o elimine usuarios ya registrados.                                                                         |
| **resultados.php**        | Vista general para el administrador donde se muestran los usuarios registrados (sección de alumnos y profesores). Incluye botones para:             |
|                           | - **Modificar usuario:** Redirige a `modificar.php`  
|                           | - **Agregar usuario:** Redirige a `admin.php`  
|                           | - **Generar PDF:** Abre `generar_pdf.php` en una nueva pestaña                                                                                       |
| **generar_pdf.php**       | Archivo usado para generar reportes PDF (disponible para administración, profesores y alumnos según el contexto).                                      |
| **calificaciones.php**    | Módulo para profesores: ingreso o modificación de calificaciones de sus alumnos, de acuerdo a la materia, grado y grupo asignados.                    |
| **estadísticas.php**      | Presenta el análisis y resumen de calificaciones ingresadas por el profesor (mejores, peores, etc).                                                    |
| **perfilalumno.php**      | Pantalla personal del alumno: muestra sus calificaciones por materia, con resúmenes (la mejor y la peor nota).                                         |
| **logout.php**            | Cierra la sesión del usuario y redirige a `login.php`.                                                                                               |
| **ayuda.html**            | Página de ayuda y preguntas frecuentes sobre el uso de la plataforma.                                                                                |
| **reglamento.html**       | Página con las normas y reglamentaciones institucionales.                                                                                            |
| **utils/**                | Librería para la generación y manejo de PDFs (utilizada por `generar_pdf.php`).                                                                        |

---

## Flujo de Navegación y Roles

### **1. Inicio de Sesión (login.php):**

- Se ingresan las credenciales.
- **Validación del login:**  
  - Si las credenciales son inválidas, se notifica el error (vía SweetAlert2) y se regresa al login.
  - Si son válidas, se redirige según el tipo de usuario.

### **2. Flujos por Rol:**

#### **Administrador:**
1. **Login → admin.php:**  
   - Desde aquí, el admin puede agregar un nuevo usuario (clic en "Enviar") o seleccionar **Modificar usuario** para editar datos existentes.
2. **Si elige Agregar Usuario:**  
   - Se envía el formulario y se redirige a `resultados.php`, donde se muestra el listado actualizado de usuarios.
3. **Desde resultados.php:**  
   - Botones disponibles:
     - **Modificar Usuario:** Redirige a `modificar.php` para editar/eliminar registros  
       > *En `modificar.php`, luego de efectuar cambios, se regresa a `resultados.php`.*
     - **Agregar Usuario:** Redirige de nuevo a `admin.php` para incluir nuevos registros.
     - **Generar PDF:** Abre `generar_pdf.php` en una nueva pestaña con el reporte PDF de los usuarios agregados.
     - **Cerrar sesión:** Redirige a `logout.php`.

4. **Rama Final (Logout):**  
   - Desde cualquier decisión final, el admin puede elegir "Cerrar sesión". En algunos casos, se plantea como una decisión:
     - ¿Desea modificar usuarios?  
       - Sí → dirige a `modificar.php` y, tras finalizar, vuelve a `resultados.php`.  
       - No → dirige a `logout.php`.

#### **Profesor:**
1. **Login → calificaciones.php:**  
   - El profesor ingresa o modifica las calificaciones de los alumnos asignados a su materia (la acción es obligatoria para cada envío).
2. **Posteriormente,** se redirige a `estadísticas.php` donde se visualizan los análisis de las calificaciones.
3. **Acciones disponibles en calificaciones/estadísticas.php:**  
   - Modificar calificaciones (volver a `calificaciones.php`)
   - Generar PDF (abre `generar_pdf.php` en nueva pestaña)
   - Cerrar sesión (redirige a `logout.php`)

#### **Alumno:**
1. **Login → perfilalumno.php:**  
   - El alumno accede a su perfil, donde se muestran sus calificaciones por materia y un resumen de sus mejores y peores resultados.
2. **Acciones disponibles:**  
   - Generar PDF (abre `generar_pdf.php` en nueva pestaña)
   - Cerrar sesión (redirige a `logout.php`)

> **Nota:** La funcionalidad de generación de PDF abre una nueva pestaña y la página original permanece activa, permitiendo seguir interactuando con la aplicación.

---

## Generación de PDFs

El archivo `generar_pdf.php` es invocado desde múltiples módulos (resultados, calificaciones/estadísticas y perfilalumno) para crear reportes PDF de los usuarios o calificaciones. La lógica para PDF reside en un directorio de utilidades (`utils`) y se integra de forma que, al generar el PDF, la pestaña original sigue mostrando el contenido (ya sea resultados, estadísticas o perfil).

---

## Notas Adicionales

- **Transversalidad del Logout:**  
  En todos los módulos (admin, profesor y alumno) existe siempre la opción de "Cerrar sesión". Para destacar esta funcionalidad en el diagrama de flujo se han utilizado líneas punteadas que conectan cada módulo con el nodo `logout.php`, mostrando que esta acción es accesible en cualquier parte del sistema.

- **Alternancia de Tema:**  
  La función de alternar entre modo claro y oscuro (implementada con cookies) está presente en todas las páginas, garantizando una experiencia uniforme sin alterar el flujo principal de la aplicación.

---

## Créditos

Desarrollado por **Flores Jasso Miguel Angel** y **Gil Solís Germán** – una solución integral para la gestión escolar, combinando control administrativo, ingreso y visualización de calificaciones y la generación de reportes en PDF.

---

## Licencia

Publicado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).


