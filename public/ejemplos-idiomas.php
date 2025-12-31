<?php
/**
 * SCRIPT DE EJEMPLO - Sistema de Idiomas
 * 
 * Este archivo muestra cómo usar el sistema de traducciones
 * en vistas y controladores.
 * 
 * Eliminar este archivo en producción o mantenerlo como referencia.
 */

// Si se accede directamente
if (!function_exists('__')) {
    // Cargar autoloader y helpers
    define('BASE_PATH', dirname(__DIR__));
    define('CORE_PATH', BASE_PATH . '/core');
    
    require_once CORE_PATH . '/helpers.php';
    require_once CORE_PATH . '/Translator.php';
    
    // Inicializar
    \Core\Translator::init();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo - Sistema de Idiomas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 30px; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; margin-bottom: 15px; font-size: 1.3em; }
        .section { background: #f9f9f9; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0; border-radius: 4px; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New'; font-size: 0.9em; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto; margin: 10px 0; }
        pre code { background: none; padding: 0; color: inherit; }
        .example { margin: 15px 0; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px; }
        .example-title { font-weight: bold; color: #007bff; margin-bottom: 10px; }
        .example-code { background: #f5f5f5; padding: 10px; border-radius: 3px; margin: 5px 0; }
        .example-result { background: #e8f5e9; padding: 10px; border-radius: 3px; margin: 5px 0; border-left: 3px solid #4caf50; }
        .tab { margin-left: 20px; }
        .info-box { background: #e3f2fd; padding: 15px; border-radius: 4px; border-left: 4px solid #2196f3; margin: 15px 0; }
        ul { margin-left: 25px; margin-top: 10px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌐 Sistema de Idiomas - Ejemplos de Uso</h1>

        <!-- Sección 1: Uso Básico -->
        <div class="section">
            <h2>1. Uso Básico en Vistas</h2>
            <p>La forma más simple de traducir textos es usar la función <code>__()</code>:</p>

            <div class="example">
                <div class="example-title">Ejemplo 1: Botones Comunes</div>
                <div class="example-code">
                    <pre><code>&lt;button&gt;&lt;?= __('common.save') ?&gt;&lt;/button&gt;
&lt;button&gt;&lt;?= __('common.cancel') ?&gt;&lt;/button&gt;
&lt;button&gt;&lt;?= __('common.delete') ?&gt;&lt;/button&gt;</code></pre>
                </div>
                <div class="example-result">
                    Resultado:<br>
                    <button style="padding: 5px 10px; margin: 3px; cursor: not-allowed;"><?= __('common.save') ?></button>
                    <button style="padding: 5px 10px; margin: 3px; cursor: not-allowed;"><?= __('common.cancel') ?></button>
                    <button style="padding: 5px 10px; margin: 3px; cursor: not-allowed;"><?= __('common.delete') ?></button>
                </div>
            </div>

            <div class="example">
                <div class="example-title">Ejemplo 2: Labels de Formularios</div>
                <div class="example-code">
                    <pre><code>&lt;form&gt;
    &lt;label&gt;&lt;?= __('employees.full_name') ?&gt;&lt;/label&gt;
    &lt;input type="text" name="full_name"&gt;

    &lt;label&gt;&lt;?= __('employees.email') ?&gt;&lt;/label&gt;
    &lt;input type="email" name="email"&gt;

    &lt;button type="submit"&gt;&lt;?= __('common.save') ?&gt;&lt;/button&gt;
&lt;/form&gt;</code></pre>
                </div>
                <div class="example-result">
                    <strong><?= __('employees.full_name') ?></strong><br>
                    <strong><?= __('employees.email') ?></strong><br>
                    <strong><?= __('common.save') ?></strong>
                </div>
            </div>
        </div>

        <!-- Sección 2: Traducciones con Reemplazos -->
        <div class="section">
            <h2>2. Traducciones Dinámicas (Con Reemplazos)</h2>
            <p>Algunos textos necesitan valores dinámicos. Usa el parámetro de reemplazos:</p>

            <div class="example">
                <div class="example-title">Ejemplo 3: Mensajes Personalizados</div>
                <div class="example-code">
                    <pre><code>&lt;?php
$userName = 'Juan';
echo __('common.welcome', [':name' => $userName]);
?&gt;</code></pre>
                </div>
                <div class="example-result">
                    <strong>Nota:</strong> Esta es una función de ejemplo. Agrega <code>':welcome' => 'Bienvenido :name'</code> en common.php para usarla.
                </div>
            </div>
        </div>

        <!-- Sección 3: Dashboard -->
        <div class="section">
            <h2>3. Dashboard</h2>
            <p>Ejemplo de traducciones en el panel principal:</p>

            <div class="example">
                <div class="example-title">Traducciones del Dashboard</div>
                <div class="example-result">
                    <strong><?= __('dashboard.total_employees') ?></strong> - Texto comúnmente usado<br>
                    <strong><?= __('dashboard.present_today') ?></strong><br>
                    <strong><?= __('dashboard.absent_today') ?></strong><br>
                    <strong><?= __('dashboard.hours_worked_today') ?></strong><br>
                </div>
            </div>
        </div>

        <!-- Sección 4: Autenticación -->
        <div class="section">
            <h2>4. Autenticación</h2>
            <p>Traducciones para formularios de login y registro:</p>

            <div class="example">
                <div class="example-title">Formulario de Login</div>
                <div class="example-result">
                    <strong><?= __('auth.login_title') ?></strong><br>
                    <strong><?= __('auth.email') ?></strong><br>
                    <strong><?= __('auth.password') ?></strong><br>
                    <strong><?= __('auth.remember_me') ?></strong><br>
                    <strong><?= __('auth.forgot_password') ?></strong><br>
                </div>
            </div>

            <div class="example">
                <div class="example-title">Registro de Usuario</div>
                <div class="example-result">
                    <strong><?= __('auth.register_title') ?></strong><br>
                    <strong><?= __('auth.first_name') ?></strong><br>
                    <strong><?= __('auth.last_name') ?></strong><br>
                    <strong><?= __('auth.email') ?></strong><br>
                    <strong><?= __('auth.password') ?></strong><br>
                </div>
            </div>
        </div>

        <!-- Sección 5: Gestión de Idiomas -->
        <div class="section">
            <h2>5. Gestión de Idiomas en PHP</h2>
            <p>Usa estas funciones en controladores:</p>

            <div class="example">
                <div class="example-title">Obtener Idioma Actual</div>
                <div class="example-code">
                    <pre><code>&lt;?php
$currentLanguage = locale();  // Retorna el idioma actual
echo "Idioma actual: " . $currentLanguage;
?&gt;</code></pre>
                </div>
                <div class="example-result">
                    <?php $currentLang = \Core\Translator::getLanguage(); ?>
                    Idioma actual: <strong><?= $currentLang ?></strong>
                </div>
            </div>

            <div class="example">
                <div class="example-title">Cambiar Idioma</div>
                <div class="example-code">
                    <pre><code>&lt;?php
locale('en');  // Cambiar a inglés
locale('es');  // Cambiar a español
locale('ca');  // Cambiar a catalán
?&gt;</code></pre>
                </div>
            </div>

            <div class="example">
                <div class="example-title">Usar Translator Directamente</div>
                <div class="example-code">
                    <pre><code>&lt;?php
use Core\Translator;

// Obtener traducción
$title = Translator::get('dashboard.title');

// Con reemplazos
$message = Translator::get('welcome', [':name' => 'Juan']);

// Verificar si existe
if (Translator::has('custom.key')) {
    echo Translator::get('custom.key');
}

// Cambiar idioma
Translator::setLanguage('en');
?&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- Sección 6: Agregar Nuevas Traducciones -->
        <div class="section">
            <h2>6. Cómo Agregar Nuevas Traducciones</h2>

            <div class="info-box">
                <strong>Paso 1:</strong> Edita el archivo correspondiente en <code>resources/lang/es/</code><br>
                <strong>Paso 2:</strong> Agrega la nueva clave y valor<br>
                <strong>Paso 3:</strong> Usa en tus vistas con <code>__('archivo.clave')</code>
            </div>

            <div class="example">
                <div class="example-title">Ejemplo: Agregar traducción de "Proyectos"</div>
                <div class="example-code">
                    <pre><code>// En resources/lang/es/projects.php
return [
    'projects' => 'Proyectos',
    'new_project' => 'Nuevo Proyecto',
    'edit_project' => 'Editar Proyecto',
    'delete_project' => 'Eliminar Proyecto',
];</code></pre>
                </div>
                <div class="example-code">
                    <pre><code>// En tu vista
&lt;h1&gt;&lt;?= __('projects.projects') ?&gt;&lt;/h1&gt;
&lt;button&gt;&lt;?= __('projects.new_project') ?&gt;&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- Sección 7: Archivos Disponibles -->
        <div class="section">
            <h2>7. Archivos de Traducciones Disponibles</h2>
            
            <div class="info-box">
                <strong>Archivos creados:</strong>
                <ul>
                    <li><code>common.php</code> - Botones y textos comunes (save, cancel, delete, etc.)</li>
                    <li><code>auth.php</code> - Login, registro, autenticación</li>
                    <li><code>dashboard.php</code> - Panel principal y estadísticas</li>
                    <li><code>employees.php</code> - Gestión de empleados</li>
                    <li><code>absences.php</code> - Gestión de ausencias y vacaciones</li>
                    <li><code>time_entries.php</code> - Fichajes y control de horas</li>
                    <li><code>admin.php</code> - Panel administrativo</li>
                </ul>
            </div>
        </div>

        <!-- Sección 8: Funciones Disponibles -->
        <div class="section">
            <h2>8. Funciones Disponibles</h2>
            
            <div class="example">
                <div class="example-title">Resumen de Funciones</div>
                <div class="tab">
                    <p><strong>__('key')</strong> - Obtener traducción (forma corta)</p>
                    <p><strong>trans('key')</strong> - Obtener traducción (forma larga)</p>
                    <p><strong>locale()</strong> - Obtener idioma actual</p>
                    <p><strong>locale('en')</strong> - Cambiar idioma</p>
                    <p><strong>Translator::get('key')</strong> - Acceso directo a la clase</p>
                    <p><strong>Translator::setLanguage('en')</strong> - Cambiar idioma via clase</p>
                    <p><strong>Translator::has('key')</strong> - Verificar si existe traducción</p>
                </div>
            </div>
        </div>

        <!-- Sección 9: Tips -->
        <div class="section">
            <h2>9. Tips y Buenas Prácticas</h2>
            
            <ul>
                <li>✅ Usa siempre <code>__()</code> en vistas en lugar de hardcodear textos</li>
                <li>✅ Organiza traducciones por módulo (common, auth, dashboard, etc.)</li>
                <li>✅ Mantén consistencia en la traducción de conceptos iguales</li>
                <li>✅ Usa claves descriptivas que sean fáciles de entender</li>
                <li>✅ El idioma del usuario se detecta automáticamente de la BD</li>
                <li>✅ No duplicues traducciones, reutiliza las de common.php</li>
                <li>✅ Agrega comentarios en los archivos de idioma si es necesario</li>
            </ul>
        </div>

        <!-- Sección 10: Debug -->
        <div class="section">
            <h2>10. Debugging y Verificación</h2>
            
            <div class="example">
                <div class="example-title">Verificar una Traducción</div>
                <div class="example-code">
                    <pre><code>&lt;?php
use Core\Translator;

if (Translator::has('dashboard.title')) {
    echo __('dashboard.title');
} else {
    echo "⚠️ Traducción no encontrada";
}
?&gt;</code></pre>
                </div>
            </div>

            <div class="info-box">
                <strong>Nota:</strong> Si una traducción no existe, el sistema retorna la clave como texto por defecto.
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 0.9em;">
            <p>📚 Para más información, consulta <strong>docs/IDIOMAS.md</strong></p>
            <p>⚠️ Este archivo es solo para referencia. Considera eliminarlo en producción.</p>
        </div>
    </div>
</body>
</html>
