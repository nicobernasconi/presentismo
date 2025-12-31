<?php
$config = require CONFIG_PATH . '/app.php';
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');

// $assetsUrl para archivos estáticos (CSS, JS, imágenes)
$assetsUrl = $scriptDir;

// $baseUrl para rutas internas
if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
    $baseUrl = $scriptDir . '/index.php?route=';
} else {
    $baseUrl = $config['url'];
}
$errors = \Core\Session::errors();
$old = $_SESSION['old'] ?? [];
\Core\Session::clearOld();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Sistema de Presentismo</title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-clock text-primary-600 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Sistema de Presentismo</h1>
            <p class="text-primary-200 mt-1">Control de fichajes y asistencia</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-1 text-center">Crear Cuenta</h2>
            <p class="text-gray-600 text-center text-sm mb-6">Regístrate para acceder al sistema</p>

            <?php if ($error = \Core\Session::error()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-800 text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/register" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= \Core\Session::csrf() ?>">
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['name']) ? 'border-red-500' : '' ?>"
                               placeholder="Juan García López"
                               required>
                    </div>
                    <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['name'][0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['email']) ? 'border-red-500' : '' ?>"
                               placeholder="tu@email.com"
                               required>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['email'][0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input :type="show ? 'text' : 'password'" 
                               id="password" 
                               name="password"
                               class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['password']) ? 'border-red-500' : '' ?>"
                               placeholder="••••••••"
                               required>
                        <button type="button" 
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Mínimo 6 caracteres</p>
                    <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password'][0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Contraseña
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input :type="show ? 'text' : 'password'" 
                               id="password_confirm" 
                               name="password_confirm"
                               class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['password_confirm']) ? 'border-red-500' : '' ?>"
                               placeholder="••••••••"
                               required>
                        <button type="button" 
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password_confirm'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password_confirm'][0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 mt-6">
                    Crear Cuenta
                </button>
            </form>

            <!-- Login Link -->
            <p class="text-center text-gray-600 text-sm mt-6">
                ¿Ya tienes cuenta? 
                <a href="<?= $baseUrl ?>/login" class="text-primary-600 hover:text-primary-700 font-medium">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <!-- Footer -->
        <p class="text-center text-primary-200 text-sm mt-6">
            © <?= date('Y') ?> Sistema de Presentismo v1.0
        </p>
    </div>

    <!-- Alpine.js para toggle password -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
