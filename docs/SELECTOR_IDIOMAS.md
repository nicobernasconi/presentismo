# 🌐 Selector de Idioma - Guía de Uso

## ¿Qué se agregó?

Se ha implementado un **selector de idioma integrado en la interfaz** del sitio. Los usuarios pueden cambiar el idioma desde el menú desplegable de perfil.

---

## 📍 Dónde está

**Ubicación:** Menú desplegable del usuario (arriba a la derecha)
1. Click en el avatar del usuario
2. Scroll hasta "Idioma"
3. Selecciona el idioma deseado

---

## 🔧 Componentes Implementados

### 1. **Controlador** ✅
**Archivo:** `app/Controllers/LanguageController.php`

```php
// Método: change()
// Validación CSRF
// Guarda en sesión y BD (si usuario autenticado)
// Redirige al referer
```

### 2. **Ruta** ✅
**Archivo:** `routes/web.php`

```php
$router->post('/cambiar-idioma', 'LanguageController@change');
```

### 3. **Layout Actualizado** ✅
**Archivo:** `app/Views/layouts/app.php`

```php
<!-- Selector de idioma en el menú de perfil -->
<!-- Muestra 3 botones: ES, EN, CA -->
<!-- Envía formulario POST a /cambiar-idioma -->
```

### 4. **Clase Translator Mejorada** ✅
**Archivo:** `core/Translator.php`

```php
// Ahora verifica la sesión primero
// Luego la BD del usuario
// Luego el idioma predeterminado
```

### 5. **Helper Functions** ✅
**Archivo:** `core/helpers.php`

```php
get_supported_languages()  // Retorna idiomas soportados
locale()                   // Obtener idioma actual
locale('en')               // Cambiar idioma
```

---

## 🚀 Cómo Funciona

### Flujo de Cambio de Idioma

```
Usuario click en idioma
        ↓
Envía POST a /cambiar-idioma
        ↓
LanguageController::change()
        ↓
┌─────────────────────────────┐
│ 1. Validar CSRF token       │
│ 2. Validar idioma soportado │
│ 3. Guardar en $_SESSION     │
│ 4. Actualizar BD (si logged)│
│ 5. Cambiar Translator       │
└─────────────────────────────┘
        ↓
Redirige al referer
        ↓
Página se recarga con nuevo idioma
```

---

## 💾 Dónde se Guarda el Idioma

### Prioridades (en orden)

1. **Sesión** (`$_SESSION['language']`) - ⚡ Más rápido
2. **Base de Datos** (`users.language`) - 💾 Persistente
3. **Configuración** (`config/language.php`) - 🔧 Predeterminado

### Inicialización

```php
// En Translator::init()
if (isset($_SESSION['language'])) {
    // Usar idioma de sesión
} elseif (Auth::check()) {
    // Usar idioma del usuario (BD)
} else {
    // Usar idioma predeterminado
}
```

---

## 🎯 Ejemplo de Uso

### En Vistas (Ya funciona)

```php
<!-- El selector aparece automáticamente en el layout -->
<!-- Usuario puede cambiar idioma en cualquier momento -->
<!-- Se refleja inmediatamente en la página -->
```

### En Controladores

```php
<?php
use Core\Translator;

// Obtener idioma actual
$currentLang = locale();  // Retorna: 'es', 'en', 'ca'

// Cambiar idioma
locale('en');

// Obtener idiomas soportados
$langs = get_supported_languages();
// Retorna: ['es' => 'Español', 'en' => 'English', 'ca' => 'Català']
?>
```

---

## 🔐 Seguridad

✅ **CSRF Protection:** Todos los cambios requieren token CSRF válido
✅ **Validación:** Solo idiomas soportados se aceptan
✅ **Sesión:** Datos guardados en sesión + BD
✅ **Redireccionamiento:** Redirige al referer (página anterior)

---

## 📱 Interfaz Visual

### Selector de Idioma

```
┌────────────────────┐
│ 🎯 Mi Perfil       │
│ ⚙️  Configuración  │
├────────────────────┤
│ 🌐 Idioma          │
│ [ES] [EN] [CA]     │
├────────────────────┤
│ 🚪 Cerrar Sesión   │
└────────────────────┘
```

**Colores:**
- Botón activo: Azul (primary-600)
- Botón inactivo: Gris claro
- Hover: Gris más oscuro

---

## ⚙️ Configuración

### Agregar Nuevo Idioma

1. **En `config/language.php`:**
```php
'supported' => [
    'es' => 'Español',
    'en' => 'English',
    'ca' => 'Català',
    'fr' => 'Français',  // ← Nuevo
]
```

2. **Crear carpeta de traducciones:**
```
resources/lang/fr/
├── common.php
├── auth.php
├── dashboard.php
└── ... (copiar de es/)
```

3. **Traducir todos los archivos**

4. **¡Listo!** El selector aparecerá automáticamente

---

## 🧪 Testing

### Probar el Cambio de Idioma

1. **Abre el sitio** → Dashboard
2. **Click en el avatar** (arriba derecha)
3. **Scroll hasta "Idioma"**
4. **Click en "EN"** (Inglés)
5. **Verifica que:**
   - Página no recarga completamente
   - Botón EN está resaltado en azul
   - Idioma se refleja en sesión

### Verificar que se Guardó

```php
<?php
// En el navegador (F12 → Network)
// POST /cambiar-idioma
// Status: 302 (redirect)

// En la BD
SELECT language FROM users WHERE id = X;
// Retorna: 'en'
?>
```

---

## 📊 Estado Actual

| Componente | Estado |
|---|---|
| Selector en interfaz | ✅ |
| Validación CSRF | ✅ |
| Guardar en sesión | ✅ |
| Guardar en BD | ✅ |
| Redireccionamiento | ✅ |
| Multiidioma | ✅ |
| Sin errores | ✅ |

---

## 🐛 Solución de Problemas

### Problema: El selector no aparece
**Solución:** Verifica que estés en una página con `AuthMiddleware` (dashboard, etc.)

### Problema: El cambio no se guarda
**Solución:** 
1. Verifica que CSRF token sea válido
2. Verifica la tabla `users` tenga el campo `language`
3. Revisa los logs de errores

### Problema: Las traducciones no cambian
**Solución:**
1. Verifica que exista `resources/lang/IDIOMA/archivo.php`
2. Recarga la página (Ctrl+F5)
3. Verifica que el Translator se haya inicializado

---

## 📝 Notas

- El cambio de idioma es **instantáneo**
- Se guarda en la **sesión actual**
- Se sincroniza con la **BD** (si usuario autenticado)
- El próximo login recordará la preferencia
- Los idiomas se obtienen de **`config/language.php`**
- El selector es **completamente responsivo**

---

## 🎓 Ejemplos de Uso Completo

### Ejemplo 1: Mostrar Idiomas en Dropdown

```php
<?php
$langs = get_supported_languages();
?>

<select name="language">
    <?php foreach ($langs as $code => $name): ?>
        <option value="<?= $code ?>" <?= locale() === $code ? 'selected' : '' ?>>
            <?= htmlspecialchars($name) ?>
        </option>
    <?php endforeach; ?>
</select>
```

### Ejemplo 2: Crear Link de Cambio Directo

```php
<?php
$langs = get_supported_languages();
?>

<div class="language-switcher">
    <?php foreach ($langs as $code => $name): ?>
        <a href="<?= $baseUrl ?>/cambiar-idioma?lang=<?= $code ?>" 
           class="<?= locale() === $code ? 'active' : '' ?>">
            <?= htmlspecialchars($name) ?>
        </a>
    <?php endforeach; ?>
</div>
```

### Ejemplo 3: En Controlador

```php
<?php
use Core\Translator;

class ReportController {
    public function export() {
        // Cambiar a inglés para exportar bilingüe
        $originalLang = locale();
        
        // Exportar en español
        locale('es');
        $esContent = $this->generateReport();
        
        // Exportar en inglés
        locale('en');
        $enContent = $this->generateReport();
        
        // Restaurar idioma original
        locale($originalLang);
    }
}
?>
```

---

**¡El selector de idioma está completamente funcional!** 🎉

Versión: 1.0.0
Fecha: 31 de Diciembre de 2025
Estado: ✅ COMPLETADO
