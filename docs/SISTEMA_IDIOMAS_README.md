# 🌐 Sistema de Idiomas - README

## ¿Qué es esto?

Se ha implementado un **sistema completo de localización (i18n)** en tu proyecto Presentismo. Ahora puedes:

✅ Traducir toda la interfaz al español
✅ Fácilmente agregar más idiomas (inglés, catalán, etc.)
✅ Cambiar el idioma dinámicamente
✅ Mantener traducciones centralizadas

---

## 🚀 Inicio Rápido

### 1️⃣ En tus Vistas (HTML/PHP)

Reemplaza textos hardcodeados con llamadas a traducción:

```php
<!-- ANTES -->
<button>Guardar</button>

<!-- DESPUÉS -->
<button><?= __('common.save') ?></button>
```

### 2️⃣ Obtener Idioma Actual

```php
<?php
$lang = locale();  // Retorna: 'es', 'en', 'ca', etc.
?>
```

### 3️⃣ Cambiar Idioma

```php
<?php
locale('en');      // Cambiar a inglés
locale('es');      // Cambiar a español
locale('ca');      // Cambiar a catalán
?>
```

---

## 📚 Documentación

Hay **3 documentos principales**:

### 1. **IMPLEMENTACION_COMPLETADA.md** ⭐ EMPIEZA AQUÍ
- 📖 Lo que se implementó
- 📊 Estadísticas (295+ traducciones)
- 🎯 Ejemplos rápidos
- ✅ Estado de verificación

### 2. **docs/IDIOMAS.md**
- 📘 Guía completa de uso
- 🔧 Configuración avanzada
- 💡 Tips y buenas prácticas
- 🐛 Debugging

### 3. **docs/GUIA_MIGRACION.md**
- 🔄 Cómo actualizar más vistas
- 📝 Ejemplos prácticos
- ⚠️ Errores comunes
- 📋 Checklist de migración

### 4. **public/ejemplos-idiomas.php** 🎨
- Página interactiva con ejemplos
- Ver traducciones en acción
- Referencia visual

---

## 📁 Estructura

```
resources/lang/es/
├── common.php              # Botones, estados, textos comunes
├── auth.php                # Login, registro
├── dashboard.php           # Panel principal
├── employees.php           # Gestión de empleados
├── absences.php            # Ausencias, vacaciones
├── time_entries.php        # Fichajes
└── admin.php               # Panel administrativo

Total: 295+ traducciones en español
```

---

## 🎯 Ejemplo Real

### Vista ANTES (sin sistema de idiomas)
```php
<div class="dashboard">
    <h1>Gestión de Empleados</h1>
    <button>Nuevo Empleado</button>
    <input placeholder="Buscar empleados">
    <table>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Departamento</th>
        <button>Guardar</button>
    </table>
</div>
```

### Vista DESPUÉS (con sistema de idiomas)
```php
<div class="dashboard">
    <h1><?= __('employees.employee_management') ?></h1>
    <button><?= __('employees.add_employee') ?></button>
    <input placeholder="<?= __('employees.search_employees') ?>">
    <table>
        <th><?= __('employees.name_column') ?></th>
        <th><?= __('employees.email_column') ?></th>
        <th><?= __('employees.department_column') ?></th>
        <button><?= __('common.save') ?></button>
    </table>
</div>
```

**Beneficios:**
- ✅ Puede cambiar el idioma globalmente
- ✅ Reutiliza traducciones
- ✅ Mantenimiento simplificado
- ✅ Preparado para múltiples idiomas

---

## 🔗 Enlaces Principales

| Documento | Propósito |
|-----------|-----------|
| **IMPLEMENTACION_COMPLETADA.md** | Resumen completo de lo realizado |
| **docs/IDIOMAS.md** | Guía de uso exhaustiva |
| **docs/GUIA_MIGRACION.md** | Cómo migrar más vistas |
| **public/ejemplos-idiomas.php** | Página interactiva con ejemplos |
| **resources/lang/es/** | Archivos de traducción |

---

## ✨ Características

| Característica | ¿Implementado? |
|---|---|
| Sistema base de traducción | ✅ |
| 295+ traducciones en español | ✅ |
| Detección automática del idioma del usuario | ✅ |
| Fallback a español por defecto | ✅ |
| Helper functions (`__()`, `trans()`) | ✅ |
| Clase `Translator` completa | ✅ |
| Soporte para múltiples idiomas | ✅ |
| Documentación completa | ✅ |
| Ejemplos prácticos | ✅ |
| Vista ejemplo actualizada | ✅ |

---

## 🚀 Funciones Disponibles

### En Vistas PHP

```php
<?= __('key.subkey') ?>                    # Obtener traducción
<?= trans('key.subkey') ?>                 # Alias largo
<?= locale() ?>                            # Obtener idioma actual
<?php locale('en'); ?>                     # Cambiar idioma
```

### En Controladores

```php
<?php
use Core\Translator;

Translator::init();                        # Inicializar
Translator::get('key.subkey');            # Obtener traducción
Translator::has('key');                    # Verificar existencia
Translator::setLanguage('en');            # Cambiar idioma
Translator::getLanguage();                # Obtener idioma actual
?>
```

---

## 🎓 Ejemplos Rápidos

### Ejemplo 1: Traduci un Botón
```php
<!-- ANTES -->
<button>Guardar</button>

<!-- DESPUÉS -->
<button><?= __('common.save') ?></button>

<!-- RESULTADO -->
<!-- En español: <button>Guardar</button> -->
<!-- En inglés (si existiera): <button>Save</button> -->
```

### Ejemplo 2: Traducir Formulario
```php
<form>
    <label><?= __('employees.full_name') ?></label>
    <input type="text">
    
    <label><?= __('employees.email') ?></label>
    <input type="email">
    
    <button><?= __('common.save') ?></button>
</form>
```

### Ejemplo 3: Dashboard
```php
<h2><?= __('dashboard.total_employees') ?></h2>
<p><?= __('dashboard.present_today') ?></p>
<button><?= __('time_entries.clock_in') ?></button>
```

---

## 📝 Próximas Tareas

### Fase 2: Migrar Más Vistas
- [ ] app/Views/dashboard/
- [ ] app/Views/employees/
- [ ] app/Views/absences/
- [ ] app/Views/time-entries/
- [ ] app/Views/admin/
- [ ] ... etc

**Guía:** Ver `docs/GUIA_MIGRACION.md`

### Fase 3: Agregar Más Idiomas
- [ ] Crear `resources/lang/en/` para inglés
- [ ] Crear `resources/lang/ca/` para catalán
- [ ] Traducir todos los archivos

### Fase 4: Selector de Idioma (Opcional)
- [ ] Dropdown en interfaz
- [ ] Guardar preferencia en BD
- [ ] Cambio dinámico

---

## ⚙️ Configuración

### Idioma Predeterminado
En `config/language.php`:
```php
'default' => 'es'  // Español
```

### Idiomas Soportados
```php
'supported' => [
    'es' => 'Español',
    'en' => 'English',
    'ca' => 'Català',
]
```

### Agregar Nuevo Idioma
1. Crear carpeta: `resources/lang/nueva_lengua/`
2. Copiar todos los .php de `es/`
3. Traducir cada archivo
4. Actualizar `'supported'` en config

---

## ✅ Checklist de Verificación

- ✅ Sistema completamente implementado
- ✅ 295+ traducciones en español
- ✅ Sin errores de sintaxis PHP
- ✅ Inicialización automática
- ✅ Documentación completa
- ✅ Ejemplos funcionales
- ✅ Vista de login actualizada como ejemplo
- ✅ Listo para producción

---

## 🆘 Solución de Problemas

### Problema: Veo "common.save" en lugar de "Guardar"
**Solución:** La traducción no está en `resources/lang/es/common.php`. Verifica que existe la clave.

### Problema: El idioma no cambia
**Solución:** Asegúrate de llamar a `locale('idioma')` antes de renderizar la vista.

### Problema: Error "Undefined function __"
**Solución:** El sistema no se inicializó. Verifica que `Translator::init()` se llame en `public/index.php`.

---

## 💡 Tips

1. **Reutiliza traducciones** - Usa `__('common.save')` en lugar de duplicar
2. **Claves descriptivas** - `'employee_name'` es mejor que `'text1'`
3. **Organiza por módulo** - Cada tipo de vista tiene su archivo
4. **Documenta cambios** - Agrega comentarios en archivos de idioma
5. **Verifica regularmente** - Busca textos hardcodeados en nuevas vistas

---

## 📞 Documentación Adicional

- **[Guía de Idiomas Completa](docs/IDIOMAS.md)** - Referencia técnica
- **[Guía de Migración](docs/GUIA_MIGRACION.md)** - Cómo migrar vistas
- **[Ejemplos Interactivos](public/ejemplos-idiomas.php)** - Ver ejemplos en vivo

---

## 🎉 ¡Listo para Usar!

El sistema está completamente implementado y documentado.

**Próximo paso:**
1. Lee `IMPLEMENTACION_COMPLETADA.md` para resumen
2. Abre `public/ejemplos-idiomas.php` para ver ejemplos
3. Comienza a migrar vistas usando `docs/GUIA_MIGRACION.md`

---

**Versión:** 1.0.0
**Estado:** ✅ Completado
**Última actualización:** 31 de diciembre de 2025

