# EjemploHtmlView - Plugin FacturaScripts

Un plugin educativo para FacturaScripts que demuestra cómo crear un módulo de gestión de clases académicas con horarios, incluyendo vistas personalizadas con HTML y JavaScript interactivo.

## 🎯 Características

- **Gestión de Clases**: Crea y edita clases académicas con información básica
  - Nombre de la clase
  - Instructor responsable
  - Fecha de creación y última actualización
  
- **Gestión de Horarios**: Sistema dinámico para asignar horarios a las clases
  - Seleccionar día de la semana
  - Establecer hora de inicio y fin
  - Agregar/modificar/eliminar horarios sin recargar la página
  
- **Interfaz Moderna**: 
  - Diseño responsive con Bootstrap
  - AJAX para operaciones sin recarga de página
  - Validación de formularios en tiempo real

- **Menú Integrado**: 
  - Menú principal: "Academia"
  - Submenú: "Clases"

## 📋 Requisitos

- FacturaScripts 2025.71 o superior
- PHP 8.4 o superior
- Base de datos compatible (MySQL, PostgreSQL, etc.)

## 🚀 Instalación

### Opción 1: Instalación Manual

1. Clona el repositorio en la carpeta de plugins:
```bash
cd /ruta/a/facturascripts/Plugins
git clone https://github.com/tuusuario/EjemploHtmlView.git
```

2. Activa el plugin desde la interfaz de FacturaScripts:
   - Ve a Administración → Plugins
   - Busca "EjemploHtmlView"
   - Haz clic en "Activar"

### Opción 2: Descarga ZIP

1. Descarga el ZIP del repositorio
2. Extrae el contenido en `Plugins/EjemploHtmlView`
3. Activa el plugin desde la administración

## 📖 Uso

### Acceder al Módulo

1. Desde el menú lateral, ve a **Academia → Clases**
2. Se abrirá la lista de clases

### Crear una Nueva Clase

1. Haz clic en el botón **"+" (Agregar)**
2. Completa los campos:
   - **Nombre**: Nombre de la clase (ej: "Matemáticas Avanzadas")
   - **Instructor**: Nombre del instructor (ej: "Dr. García")
3. Haz clic en **"Guardar"**

### Agregar Horarios a una Clase

1. Desde la lista, haz clic en una clase para editarla
2. Ve a la pestaña **"Horario"**
3. Haz clic en el botón **"Añadir horario"**
4. Completa los datos del horario:
   - **Día**: Selecciona el día de la semana
   - **Desde**: Hora de inicio (ej: 09:00)
   - **Hasta**: Hora de fin (ej: 11:00)
5. Haz clic en **"Guardar"** para guardar todos los cambios

### Editar Horarios

1. Abre la clase y ve a la pestaña **"Horario"**
2. Modifica los horarios existentes
3. Haz clic en **"Guardar"** para aplicar los cambios

### Eliminar Horarios

1. En la pestaña **"Horario"**, haz clic en el botón **"Eliminar"** del horario que deseas quitar
2. Haz clic en **"Guardar"** para confirmar

## 🏗️ Estructura del Plugin

```
EjemploHtmlView/
├── README.md                          # Este archivo
├── Init.php                           # Inicializador del plugin
├── Model/
│   ├── AcademiaClase.php             # Modelo de clases
│   └── AcademiaClaseHorario.php      # Modelo de horarios
├── Controller/
│   ├── ListAcademiaClase.php         # Controlador de listado
│   └── EditAcademiaClase.php         # Controlador de edición
├── XMLView/
│   ├── ListAcademiaClase.xml         # Vista de listado
│   └── EditAcademiaClase.xml         # Vista de edición
├── View/
│   └── EditAcademiaClaseHorario.html.twig  # Vista de horarios (HTML personalizado)
├── Table/
│   ├── academias_clases.xml          # Estructura tabla clases
│   └── academias_clases_horarios.xml # Estructura tabla horarios
└── Translation/
    ├── es_ES.json                    # Traducciones al español
    └── en_EN.json                    # Traducciones al inglés
```

## 🔧 Componentes Principales

### Modelos (Model/)

#### AcademiaClase.php
Representa una clase académica con:
- `id`: Identificador único
- `name`: Nombre de la clase
- `instructor`: Nombre del instructor
- `creationdate`: Fecha de creación
- `lastupdate`: Última actualización
- `nick`: Usuario que creó/actualizó
- `lastnick`: Último usuario que actualizó

Método importante: `getSchedule()` - Obtiene todos los horarios de la clase

#### AcademiaClaseHorario.php
Representa un horario de clase con:
- `id`: Identificador único
- `idclase`: Referencia a la clase
- `day`: Día de la semana (0-6, donde 0 es domingo)
- `hourstart`: Hora de inicio (formato HH:MM)
- `hourend`: Hora de fin (formato HH:MM)
- `creationdate`: Fecha de creación
- `lastupdate`: Última actualización
- `nick`: Usuario que creó

### Controladores (Controller/)

#### ListAcademiaClase.php
- Muestra la lista de todas las clases
- Permite ordenar por instructor o nombre
- Permite buscar clases
- Menú: Academia → Clases

#### EditAcademiaClase.php
- Edita una clase individual
- Gestiona los horarios mediante AJAX
- Métodos especiales:
  - `addSchedule()`: Agrega un nuevo horario (sin guardar en BD)
  - `updateSchedule()`: Guarda todos los horarios modificados
  - `renderLine()`: Genera HTML para cada fila de horario
  - `getSchedule()`: Obtiene los horarios de la clase actual

### Vistas

#### EditAcademiaClaseHorario.html.twig
Vista personalizada que gestiona:
- Renderización de horarios existentes
- Formulario AJAX para agregar/modificar horarios
- Interacción dinámica sin recarga de página
- Validación de datos en cliente

## 🛠️ Características Técnicas Importantes

### AJAX Implementation
El plugin implementa operaciones AJAX para:
- Agregar nuevos horarios sin recargar la página
- Guardar múltiples horarios en una sola operación
- Validación del token de seguridad multi-request

### Validación de Datos
- Campos requeridos en formularios
- Validación de horas (formato HH:MM)
- Selección obligatoria de día
- Validación del modelo antes de guardar

### Transacciones de Base de Datos
- Las operaciones de actualización usan transacciones
- Rollback automático en caso de error
- Consistencia garantizada

### Traducciones
Soporta múltiples idiomas:
- Español (es_ES)
- Inglés (en_EN)
Fácil de extender con otros idiomas

## 💡 Conceptos Educativos

Este plugin es un excelente ejemplo para aprender:

1. **Desarrollo de Modelos**: Cómo crear modelos de datos compatibles con FacturaScripts 2025+
2. **Controladores Extendidos**: Uso de `EditController` y `ListController`
3. **Vistas XML**: Configuración de vistas usando XMLView
4. **Vistas HTML Personalizadas**: Creación de vistas Twig con CSS/JavaScript
5. **AJAX**: Comunicación asincrónica con el servidor
6. **Traducciones**: Implementación de i18n multiidioma
7. **Base de Datos**: Creación de tablas y relaciones
8. **Validaciones**: Validación en cliente y servidor
9. **Transacciones**: Manejo de operaciones multiples con rollback

## 🐛 Troubleshooting

### El botón "Añadir horario" no funciona
- Verifica que JavaScript esté habilitado
- Comprueba la consola del navegador (F12) para ver errores
- Asegúrate de que tienes permisos de actualización

### Los horarios no se guardan
- Verifica que la base de datos tiene las tablas creadas
- Comprueba los logs de FacturaScripts
- Asegúrate de que tienes permisos de escritura en la base de datos

### Problemas de traducciones
- Borra la caché de FacturaScripts: `MyFiles/Cache/`
- Recarga la página del navegador (Ctrl+Shift+R)

## 📝 Licencia

Este plugin es de código abierto. Siéntete libre de usarlo, modificarlo y compartirlo.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si encuentras bugs o tienes sugerencias, abre un issue o pull request.

## 📚 Recursos Útiles

- [Documentación FacturaScripts](https://facturascripts.com/documentacion)
- [Comunidad FacturaScripts](https://facturascripts.com)
- [GitHub FacturaScripts](https://github.com/Artexacta/facturascripts)

## ✨ Changelog

### v1.0.0 (2026-03-06)
- Versión inicial
- Gestión completa de clases
- Sistema dinámico de horarios con AJAX
- Soporte multiidioma (ES/EN)
- Menú integrado "Academia"

---

**Desarrollado para FacturaScripts 2025.71+**

¿Preguntas? Abre un issue en el repositorio o contacta al desarrollador.
