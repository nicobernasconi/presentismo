<!DOCTYPE html>
<html lang="es">
<head>
        <?php
        // URLs para assets y rutas
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        $assetsUrl = $scriptDir;
        
        if (!isset($baseUrl)) {
            if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
                $baseUrl = $scriptDir . '/index.php?route=';
            } else {
                $config = require CONFIG_PATH . '/app.php';
                $baseUrl = $config['url'] ?? '/presentismo/public';
            }
        }
        ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Presentismo</title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <!-- Logo/Header -->
                <div class="text-center mb-8">
                    <i class="fas fa-crown text-amber-400 text-4xl mb-4 block"></i>
                    <h1 class="text-3xl font-bold text-gray-900">Panel Administrativo</h1>
                    <p class="text-gray-600 mt-2">Acceso Restringido</p>
                </div>

                <!-- Messages -->
                <?php if (!empty($error)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-700 text-sm"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-700 text-sm"><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?></p>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="<?= $baseUrl ?>/admin/login" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                               placeholder="admin@presentismo.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                               placeholder="••••••••">
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-amber-600 rounded">
                        <label for="remember" class="ml-2 text-sm text-gray-700">Recuérdame</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Ingresar
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-xs text-gray-500">
                        Credenciales de prueba:<br>
                        Email: <strong>admin@presentismo.com</strong><br>
                        Pass: <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
