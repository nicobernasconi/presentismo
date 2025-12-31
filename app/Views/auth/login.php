<?php
/**
 * Vista: Login
 * Variables: $title, $baseUrl, $assetsUrl
 */

// Usar las variables del controlador, con fallback si no existen
if (!isset($assetsUrl)) {
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $assetsUrl = $scriptDir;
}
if (!isset($baseUrl)) {
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $baseUrl = $scriptDir . '/index.php?route=';
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
    <title><?= __('auth.login_title') ?> - Sistema de Presentismo</title>
    
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
            <p class="text-primary-200 mt-1"><?= __('dashboard.welcome_to_dashboard') ?></p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 text-center"><?= __('auth.login_title') ?></h2>

            <?php if ($error = \Core\Session::error()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-800 text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/login" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= \Core\Session::csrf() ?>">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        <?= __('auth.email') ?>
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
                        <?= __('auth.password') ?>
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
                    <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password'][0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-600"><?= __('auth.remember_me') ?></span>
                    </label>
                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700">
                        <?= __('auth.forgot_password') ?>
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <?= __('auth.sign_in') ?>
                </button>
            </form>

            <!-- Register Link -->
            <p class="text-center text-gray-600 text-sm mt-6">
                <?= __('auth.dont_have_account') ?> 
                <a href="<?= $baseUrl ?>/register" class="text-primary-600 hover:text-primary-700 font-medium">
                    <?= __('auth.create_one') ?>
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
