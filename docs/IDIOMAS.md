# Sistema de Idiomas - Presentismo

## Descripción

Sistema completo de localización (i18n) que permite traducir toda la interfaz del usuario a múltiples idiomas. Actualmente implementado para español, con estructura preparada para inglés y catalán.

## Estructura

```
resources/
├── lang/
│   └── es/
│       ├── common.php          # Traducciones comunes
│       ├── auth.php            # Autenticación
│       ├── dashboard.php       # Panel de control
│       ├── employees.php       # Gestión de empleados
│       ├── absences.php        # Ausencias
│       ├── time_entries.php    # Fichajes
│       └── admin.php           # Panel administrativo

config/
└── language.php                # Configuración de idiomas
```

## Configuración

### Idiomas Soportados

En `config/language.php`:

```php
'supported' => [
    'es' => 'Español',
    'en' => 'English',
    'ca' => 'Català',
]
```

### Idioma Predeterminado

```php
'default' => 'es'
```

## Uso en Vistas

### Función Helper `__()`

La forma más simple de traducir textos en las vistas:

```php
<!-- Uso básico -->
<h1><?= __('dashboard.title') ?></h1>

<!-- Con reemplazos -->
<p><?= __('auth.welcome_user', [':name' => 'Juan']) ?></p>
```

### Función `trans()`

Alias de `__()`:

```php
<button><?= trans('common.save') ?></button>
```

### Obtener Idioma Actual

```php
<?php
$currentLanguage = locale();  // Retorna: 'es'
locale('en');                 // Cambia a inglés
?>
```

## Uso en PHP (Controladores)

```php
<?php
use Core\Translator;

// En un controlador
class UserController extends Controller {
    public function show($id) {
        // Obtener traducción
        $title = Translator::get('users.user_details');
        
        // Con reemplazos
        $message = Translator::get('auth.welcome_user', [
            ':name' => $user->first_name
        ]);
        
        // Verificar si existe una traducción
        if (Translator::has('custom.key')) {
            // ...
        }
        
        // Cambiar idioma
        Translator::setLanguage('en');
    }
}
```

## Estructura de Archivos de Idioma

Cada archivo de idioma retorna un array asociativo:

```php
<?php
// resources/lang/es/common.php
return [
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'delete' => 'Eliminar',
    // ...
];
```

### Formato de Claves

Las claves siguen el patrón `archivo.clave`:

```php
__('common.save')        // archivo: common, clave: save
__('dashboard.title')    // archivo: dashboard, clave: title
__('auth.email')         // archivo: auth, clave: email
```

## Agregar Nuevas Traducciones

### 1. Editar el archivo correspondiente

```php
// resources/lang/es/time_entries.php
return [
    'clock_in' => 'Fichar Entrada',
    'clock_out' => 'Fichar Salida',
    'new_feature' => 'Mi Nueva Función',  // ← Agregar aquí
];
```

### 2. Usar en la vista

```php
<button><?= __('time_entries.new_feature') ?></button>
```

## Agregar un Nuevo Idioma

### 1. Crear carpeta del idioma

```bash
mkdir resources/lang/en
```

### 2. Copiar y traducir archivos

```bash
cp resources/lang/es/common.php resources/lang/en/common.php
# Editar y traducir el contenido
```

### 3. Actualizar configuración

En `config/language.php`:

```php
'supported' => [
    'es' => 'Español',
    'en' => 'English',
    'ca' => 'Català',
    'fr' => 'Français',  // ← Nuevo idioma
]
```

## Cambiar Idioma de Usuario

El sistema detecta automáticamente el idioma del usuario autenticado desde la columna `language` de la tabla `users`.

```php
// En la BD
UPDATE users SET language = 'en' WHERE id = 1;

// En PHP
$user->update(['language' => 'en']);
Translator::setLanguage('en');
```

## Inicialización Automática

El Translator se inicializa automáticamente en `public/index.php`:

```php
use Core\Translator;

// Inicializar con idioma del usuario o predeterminado
Translator::init();
```

La clase `Translator` detecta automáticamente:
1. El idioma del usuario autenticado (campo `language`)
2. Si no existe usuario, usa el idioma predeterminado de configuración

## Ejemplos Completos

### Ejemplo 1: Traducir Botón Común

```php
<!-- Antes -->
<button class="btn">Guardar</button>

<!-- Después -->
<button class="btn"><?= __('common.save') ?></button>
```

### Ejemplo 2: Traducir Formulario con Etiquetas

```php
<!-- Antes -->
<form>
    <label>Nombre Completo</label>
    <input type="text" name="full_name">
    
    <label>Correo</label>
    <input type="email" name="email">
    
    <button type="submit">Enviar</button>
</form>

<!-- Después -->
<form>
    <label><?= __('employees.full_name') ?></label>
    <input type="text" name="full_name">
    
    <label><?= __('common.email') ?></label>
    <input type="email" name="email">
    
    <button type="submit"><?= __('common.submit') ?></button>
</form>
```

### Ejemplo 3: Traducción Dinámica en Controlador

```php
<?php

namespace App\Controllers;

use Core\Controller;
use Core\Translator;

class ReportController extends Controller {
    public function export() {
        $format = $_GET['format'] ?? 'pdf';
        
        $message = Translator::get('reports.exporting_format', [
            ':format' => strtoupper($format)
        ]);
        
        // Salida: "Exportando en formato PDF..."
    }
}
```

## Archivos de Idioma Disponibles

### common.php
Traducciones comunes del sistema:
- Botones: save, cancel, delete, edit, create...
- Estados: active, inactive, pending, approved...
- Textos genéricos: dashboard, settings, profile...

### auth.php
Autenticación y registro:
- login_title, register_title
- email, password, remember_me
- Mensajes de error y éxito

### dashboard.php
Panel de control:
- Dashboard principal
- Estadísticas: total_employees, present_today...
- Acciones rápidas

### employees.php
Gestión de empleados:
- Campos: full_name, email, phone, department...
- Acciones: add_employee, edit_employee, delete_employee...
- Mensajes y validaciones

### absences.php
Gestión de ausencias:
- Tipos: vacation, sick_leave, maternity_leave...
- Estados: pending, approved, rejected...
- Estadísticas de vacaciones

### time_entries.php
Sistema de fichajes:
- Acciones: clock_in, clock_out, check_in...
- Campos: entry_time, exit_time, break_duration...
- Resúmenes: hours_this_week, extra_hours...

### admin.php
Panel administrativo:
- Gestión de empresas, usuarios, planes
- Estadísticas administrativas
- Configuración del sistema

## Tips y Buenas Prácticas

### 1. Mantener Coherencia

Usa la misma traducción para conceptos iguales:

```php
// ✓ Bien - consistente
'save' => 'Guardar',          // common.php
'save_changes' => 'Guardar cambios',  // También en el archivo apropiado

// ✗ Mal - inconsistente
'save' => 'Guardar',
'submit' => 'Grabar',  // Diferente palabra para lo mismo
```

### 2. Claves Descriptivas

```php
// ✓ Bien - descriptivo
'employee_not_found' => 'Empleado no encontrado'

// ✗ Mal - poco claro
'error_1' => 'Empleado no encontrado'
```

### 3. Comentarios en Archivos

```php
return [
    // Acciones comunes
    'save' => 'Guardar',
    'cancel' => 'Cancelar',

    // Validación
    'required' => 'Este campo es requerido',
];
```

### 4. Reutilizar Traducciones

```php
// En lugar de duplicar:
__('common.save')      // Usa la traducción común en todas partes
__('common.delete')
__('common.edit')
```

## Debugging

### Ver Idioma Actual

```php
<?php echo locale(); ?>
<!-- Salida: es -->
```

### Verificar si Existe Traducción

```php
<?php
use Core\Translator;

if (Translator::has('dashboard.title')) {
    echo __('dashboard.title');
} else {
    echo 'Traducción no encontrada';
}
?>
```

### Ver Todas las Traducciones Cargadas

```php
<?php
// En el controlador o vista de debug
use Core\Translator;
dd(Translator::getAll());  // Función personalizada si necesitas
?>
```

## Notas Importantes

- El Translator se cachea automáticamente en memoria
- Las traducciones se cargan bajo demanda (lazy loading)
- Si una traducción no existe, retorna la clave como texto
- El sistema mantiene automáticamente la consistencia de idiomas
- Los cambios en archivos de idioma se aplican inmediatamente (sin caché persistente)

## Próximos Pasos

1. ✅ **Sistema base implementado** - Ya disponible
2. 📝 **Traducir todas las vistas** - En progreso
3. 🌐 **Agregar más idiomas** - Estructura lista
4. 🎯 **Validación de traducciones** - Pendiente
5. 📊 **Reporte de cobertura** - Pendiente

