# 📚 Guía de Migración - Actualizar Vistas a Sistema de Idiomas

## Objetivo
Convertir vistas con textos hardcodeados a usar el sistema de idiomas.

## 🔄 Proceso General

### Paso 1: Identificar los Textos Hardcodeados
Busca en la vista:
- Textos entre etiquetas HTML
- Labels de formularios
- Títulos y encabezados
- Botones
- Mensajes de placeholder

**Ejemplo (ANTES):**
```php
<h2>Gestión de Empleados</h2>
<label>Nombre Completo</label>
<input placeholder="Ingresa tu nombre">
<button>Guardar</button>
```

### Paso 2: Buscar/Crear Archivo de Traducción
Determina en qué archivo de idioma va:
- ¿Es empleados? → `employees.php`
- ¿Es auténtica? → `auth.php`
- ¿Es botón común? → `common.php`
- ¿Es dashboard? → `dashboard.php`

### Paso 3: Agregar Traducción
En `resources/lang/es/{archivo}.php`:
```php
return [
    'employee_management' => 'Gestión de Empleados',
    'full_name' => 'Nombre Completo',
    'enter_name' => 'Ingresa tu nombre',
    'save' => 'Guardar',  // o usar common.save
];
```

### Paso 4: Actualizar Vista
Reemplaza los textos:
```php
<h2><?= __('employees.employee_management') ?></h2>
<label><?= __('employees.full_name') ?></label>
<input placeholder="<?= __('employees.enter_name') ?>">
<button><?= __('common.save') ?></button>
```

---

## 📋 Lista de Vistas por Actualizar

### Vistas de Autenticación
```
app/Views/auth/
├── login.php              ✅ YA ACTUALIZADO
├── register.php           ⏳ Por hacer
└── register_company.php   ⏳ Por hacer
```

### Vistas de Dashboard
```
app/Views/dashboard/
├── index.php              ⏳ Por hacer
└── no_tenant.php          ⏳ Por hacer
```

### Vistas de Empleados
```
app/Views/employees/
├── form.php               ⏳ Por hacer
├── index.php              ⏳ Por hacer
└── show.php               ⏳ Por hacer
```

### Vistas de Ausencias
```
app/Views/absences/
├── create.php             ⏳ Por hacer
└── index.php              ⏳ Por hacer
```

### Vistas de Fichajes
```
app/Views/time-entries/
├── create.php             ⏳ Por hacer
└── index.php              ⏳ Por hacer
```

### Vistas de Departamentos
```
app/Views/departments/
├── form.php               ⏳ Por hacer
├── index.php              ⏳ Por hacer
└── show.php               ⏳ Por hacer
```

### Y más...

---

## 🎯 Ejemplos Prácticos

### Ejemplo 1: Actualizar app/Views/employees/index.php

**ANTES:**
```php
<div class="page-header">
    <h1>Gestión de Empleados</h1>
    <p>Total: 45 empleados</p>
</div>

<div class="actions">
    <button>Nuevo Empleado</button>
    <input placeholder="Buscar por nombre, email o DNI">
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Departamento</th>
            <th>Puesto</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>
```

**DESPUÉS:**
```php
<div class="page-header">
    <h1><?= __('employees.employee_management') ?></h1>
    <p><?= __('common.total_employees') ?>: 45</p>
</div>

<div class="actions">
    <button><?= __('employees.add_employee') ?></button>
    <input placeholder="<?= __('employees.search_employees') ?>">
</div>

<table>
    <thead>
        <tr>
            <th><?= __('employees.name_column') ?></th>
            <th><?= __('employees.email_column') ?></th>
            <th><?= __('employees.department_column') ?></th>
            <th><?= __('employees.position_column') ?></th>
            <th><?= __('employees.status_column') ?></th>
            <th><?= __('common.actions') ?></th>
        </tr>
    </thead>
</table>
```

### Ejemplo 2: Actualizar app/Views/dashboard/index.php

**ANTES:**
```php
<div class="dashboard-grid">
    <div class="card">
        <h3>Total Empleados</h3>
        <p class="number">45</p>
    </div>
    <div class="card">
        <h3>Presentes Hoy</h3>
        <p class="number">42</p>
    </div>
    <div class="card">
        <h3>Ausentes Hoy</h3>
        <p class="number">3</p>
    </div>
    <div class="card">
        <h3>Horas Trabajadas Hoy</h3>
        <p class="number">245.5 h</p>
    </div>
</div>

<button>Solicitar Ausencia</button>
<button>Fichar Entrada</button>
<button>Fichar Salida</button>
```

**DESPUÉS:**
```php
<div class="dashboard-grid">
    <div class="card">
        <h3><?= __('dashboard.total_employees') ?></h3>
        <p class="number">45</p>
    </div>
    <div class="card">
        <h3><?= __('dashboard.present_today') ?></h3>
        <p class="number">42</p>
    </div>
    <div class="card">
        <h3><?= __('dashboard.absent_today') ?></h3>
        <p class="number">3</p>
    </div>
    <div class="card">
        <h3><?= __('dashboard.hours_worked_today') ?></h3>
        <p class="number">245.5 h</p>
    </div>
</div>

<button><?= __('absences.request_absence') ?></button>
<button><?= __('time_entries.clock_in') ?></button>
<button><?= __('time_entries.clock_out') ?></button>
```

### Ejemplo 3: Actualizar Formularios

**ANTES:**
```php
<form method="POST">
    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" required>
        <small class="error"><?= $errors['name'] ?? '' ?></small>
    </div>

    <div class="form-group">
        <label for="email">Correo</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="department">Departamento</label>
        <select id="department" name="department">
            <option>Selecciona un departamento</option>
        </select>
    </div>

    <button type="submit">Guardar</button>
    <button type="reset">Cancelar</button>
</form>
```

**DESPUÉS:**
```php
<form method="POST">
    <div class="form-group">
        <label for="name"><?= __('employees.first_name') ?></label>
        <input type="text" id="name" name="name" required>
        <?php if ($errors['name'] ?? false): ?>
        <small class="error"><?= $errors['name'] ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email"><?= __('employees.email') ?></label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="department"><?= __('employees.department') ?></label>
        <select id="department" name="department">
            <option><?= __('common.select_one') ?></option>
        </select>
    </div>

    <button type="submit"><?= __('common.save') ?></button>
    <button type="reset"><?= __('common.cancel') ?></button>
</form>
```

---

## 🔍 Búsqueda y Reemplazo (VSCode)

Para acelerar la migración, usa Find and Replace:

### Buscar botones comunes

**Buscar:**
```
>Guardar<
```

**Reemplazar con:**
```
><?= __('common.save') ?><
```

### Buscar labels

**Buscar:**
```
<label.*?>.*?Nombre.*?</label>
```

**Usar regex para encontrar y reemplazar manualmente**

---

## 📝 Checklist de Migración

Para cada vista:

- [ ] **Identificar todos los textos hardcodeados**
  - [ ] Títulos (h1, h2, h3)
  - [ ] Labels de formularios
  - [ ] Botones
  - [ ] Placeholders
  - [ ] Mensajes
  - [ ] Estados
  - [ ] Encabezados de tablas

- [ ] **Crear/Actualizar traducciones**
  - [ ] Agregar a archivo correcto
  - [ ] Usar claves consistentes
  - [ ] Verificar que no haya duplicados

- [ ] **Actualizar vista**
  - [ ] Reemplazar cada texto
  - [ ] Usar `__('archivo.clave')`
  - [ ] Verificar sintaxis

- [ ] **Probar en navegador**
  - [ ] Que se vea correctamente
  - [ ] Que las traducciones aparezcan
  - [ ] Que no haya errores de PHP

- [ ] **Validar funcionalidad**
  - [ ] Que los formularios sigan funcionando
  - [ ] Que los links sigan funcionando
  - [ ] Que los estilos se apliquen correctamente

---

## 🚀 Automatización (Script PHP)

Si tienes muchas vistas, puedes crear un script para buscar textos hardcodeados:

```php
<?php
// Buscar textos hardcodeados en vistas
$viewPath = __DIR__ . '/app/Views/';
$pattern = '/>[^<]*[A-Z][a-záéíóúñ\s]+</';  // Busca textos en español

function searchInFiles($dir, $pattern) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file);
            if (preg_match_all($pattern, $content, $matches)) {
                echo "En " . $file->getPathname() . ":\n";
                foreach ($matches[0] as $match) {
                    echo "  - $match\n";
                }
            }
        }
    }
}

searchInFiles($viewPath, $pattern);
?>
```

---

## 💡 Tips Importantes

### 1. **Reutiliza Traducciones**
Si ya existe una traducción en `common.php`, úsala:
```php
<!-- NO HACER: -->
<?= __('employees.save') ?>
<?= __('dashboard.save') ?>

<!-- HACER: -->
<?= __('common.save') ?>  <!-- Una sola vez -->
```

### 2. **Agrupa por Archivo**
Si una traducción se usa en múltiples lugares del módulo, agrégala al archivo del módulo:
```php
// employees.php
'employee_management' => 'Gestión de Empleados',  // Específico de empleados
'save' => __('common.save'),  // ❌ NO HAGAS ESTO

// Mejor: usa directamente common.save en todas partes
```

### 3. **Claves Descriptivas**
```php
// ✅ BIEN
'total_employees' => 'Total Empleados'
'present_today' => 'Presentes Hoy'

// ❌ MAL
'text1' => 'Total Empleados'
'text2' => 'Presentes Hoy'
```

### 4. **Orden Alfabético**
Mantén las traducciones en orden alfabético dentro de cada archivo para fácil búsqueda.

### 5. **Comentarios**
Agrega comentarios en los archivos de idioma si es necesario:
```php
return [
    // Acciones principales
    'add_employee' => 'Agregar Empleado',
    'edit_employee' => 'Editar Empleado',
    
    // Estados
    'active' => 'Activo',
    'inactive' => 'Inactivo',
];
```

---

## ⚠️ Errores Comunes

### Error 1: Olvidas el archivo
```php
// ❌ INCORRECTO
<?= __('save') ?>  // ¿De qué archivo?

// ✅ CORRECTO
<?= __('common.save') ?>
```

### Error 2: Olvidas agregar la traducción
```php
// ❌ INCORRECTO
<?= __('employees.custom_text') ?>  // Pero no está en employees.php

// ✅ CORRECTO
// Primero en employees.php:
'custom_text' => 'Mi texto',
// Luego en vista:
<?= __('employees.custom_text') ?>
```

### Error 3: Mezclas español e inglés
```php
// ❌ INCORRECTO
<?= __('employees.empleado_name') ?>  // Mezclas idiomas

// ✅ CORRECTO
<?= __('employees.employee_name') ?>  // Consistente
```

### Error 4: No escapas valores dinámicos
```php
// ❌ POTENCIAL PROBLEMA (si $userInput contiene HTML)
<?= __('welcome', [':name' => $userInput]) ?>

// ✅ SEGURO
<?= __('welcome', [':name' => htmlspecialchars($userInput)]) ?>
```

---

## 📊 Progreso

Puedes rastrear tu progreso así:

```
✅ Login - HECHO
✅ Registro - HECHO
⏳ Dashboard - EN PROGRESO (50%)
⏳ Empleados - 25%
⏳ Ausencias - 0%
⏳ Fichajes - 0%
⏳ Admin - 0%
```

---

## 🎯 Objetivo Final

Cuando termines la migración:
- ✅ 100% de textos visibles para usuarios estarán traducidos
- ✅ Toda la interfaz será multiidioma
- ✅ Nuevas vistas usarán idiomas automáticamente
- ✅ Mantenimiento simplificado

---

## 📞 Preguntas Frecuentes

**P: ¿Necesito actualizar todas las vistas a la vez?**
R: No. Puedes hacerlo gradualmente. Las vistas no actualizadas seguirán funcionando.

**P: ¿Qué pasa si tengo un texto que no está traducido?**
R: El sistema retorna la clave como texto. Así sabrás qué falta traducir.

**P: ¿Puedo cambiar el idioma dinámicamente?**
R: Sí. Usa `locale('en')` para cambiar al inglés, `locale('es')` para volver a español.

**P: ¿Cómo agrego un nuevo idioma?**
R: Crea `resources/lang/en/` y copia/traduce todos los archivos de `es/`.

---

**¡Felicidades por tu nueva interfaz multiidioma!** 🚀
