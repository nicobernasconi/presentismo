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

// Planes disponibles
$plans = [
    'basic' => ['name' => 'Básico', 'price' => '9€/mes', 'employees' => 10],
    'professional' => ['name' => 'Profesional', 'price' => '29€/mes', 'employees' => 50],
    'enterprise' => ['name' => 'Enterprise', 'price' => '79€/mes', 'employees' => 'Ilimitados'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empresa - Sistema de Presentismo</title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="../../" class="inline-flex items-center justify-center">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center mb-4">
                    <i class="fas fa-clock text-primary-600 text-3xl"></i>
                </div>
            </a>
            <h1 class="text-2xl font-bold text-white">Registra tu Empresa</h1>
            <p class="text-primary-200 mt-1">Comienza a gestionar la asistencia de tu equipo</p>
        </div>

        <!-- Registro Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8" x-data="{ step: 1, plan: '<?= htmlspecialchars($old['plan'] ?? 'professional') ?>' }">
            
            <?php if ($error = \Core\Session::error()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-800 text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Progress Steps -->
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center">
                    <div :class="step >= 1 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-600'" 
                         class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                        1
                    </div>
                    <div class="w-16 h-1 mx-2" :class="step >= 2 ? 'bg-primary-600' : 'bg-gray-200'"></div>
                    <div :class="step >= 2 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-600'" 
                         class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                        2
                    </div>
                    <div class="w-16 h-1 mx-2" :class="step >= 3 ? 'bg-primary-600' : 'bg-gray-200'"></div>
                    <div :class="step >= 3 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-600'" 
                         class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">
                        3
                    </div>
                </div>
            </div>

            <form action="<?= $baseUrl ?>register-company" method="POST" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?= \Core\Session::csrf() ?>">
                <input type="hidden" name="plan" :value="plan">

                <!-- Step 1: Selección de Plan -->
                <div x-show="step === 1" x-transition>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 text-center">Elige tu plan</h2>
                    
                    <div class="grid md:grid-cols-3 gap-4 mb-8">
                        <!-- Plan Básico -->
                        <div @click="plan = 'basic'" 
                             :class="plan === 'basic' ? 'border-primary-600 bg-primary-50 ring-2 ring-primary-600' : 'border-gray-200 hover:border-primary-300'"
                             class="border-2 rounded-xl p-6 cursor-pointer transition-all">
                            <div class="text-center">
                                <h3 class="font-bold text-gray-900">Básico</h3>
                                <div class="text-2xl font-bold text-primary-600 my-2">9€<span class="text-sm text-gray-500">/mes</span></div>
                                <p class="text-sm text-gray-600">Hasta 10 empleados</p>
                            </div>
                        </div>

                        <!-- Plan Profesional -->
                        <div @click="plan = 'professional'" 
                             :class="plan === 'professional' ? 'border-primary-600 bg-primary-50 ring-2 ring-primary-600' : 'border-gray-200 hover:border-primary-300'"
                             class="border-2 rounded-xl p-6 cursor-pointer transition-all relative">
                            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-yellow-400 text-yellow-900 px-3 py-0.5 rounded-full text-xs font-semibold">
                                Popular
                            </div>
                            <div class="text-center">
                                <h3 class="font-bold text-gray-900">Profesional</h3>
                                <div class="text-2xl font-bold text-primary-600 my-2">29€<span class="text-sm text-gray-500">/mes</span></div>
                                <p class="text-sm text-gray-600">Hasta 50 empleados</p>
                            </div>
                        </div>

                        <!-- Plan Enterprise -->
                        <div @click="plan = 'enterprise'" 
                             :class="plan === 'enterprise' ? 'border-primary-600 bg-primary-50 ring-2 ring-primary-600' : 'border-gray-200 hover:border-primary-300'"
                             class="border-2 rounded-xl p-6 cursor-pointer transition-all">
                            <div class="text-center">
                                <h3 class="font-bold text-gray-900">Enterprise</h3>
                                <div class="text-2xl font-bold text-primary-600 my-2">79€<span class="text-sm text-gray-500">/mes</span></div>
                                <p class="text-sm text-gray-600">Empleados ilimitados</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" @click="step = 2" 
                                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-semibold">
                            Continuar <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Datos de la Empresa -->
                <div x-show="step === 2" x-transition>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 text-center">Datos de la Empresa</h2>
                    
                    <div class="grid md:grid-cols-2 gap-5">
                        <!-- Nombre de la empresa -->
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de la empresa <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                <input type="text" id="company_name" name="company_name" 
                                       value="<?= htmlspecialchars($old['company_name'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['company_name']) ? 'border-red-500' : '' ?>"
                                       placeholder="Mi Empresa S.L." required>
                            </div>
                            <?php if (isset($errors['company_name'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['company_name'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- CIF/NIF -->
                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">
                                CIF/NIF <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input type="text" id="tax_id" name="tax_id" 
                                       value="<?= htmlspecialchars($old['tax_id'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['tax_id']) ? 'border-red-500' : '' ?>"
                                       placeholder="B12345678" required>
                            </div>
                            <?php if (isset($errors['tax_id'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['tax_id'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Email de la empresa -->
                        <div>
                            <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email de la empresa <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="company_email" name="company_email" 
                                       value="<?= htmlspecialchars($old['company_email'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['company_email']) ? 'border-red-500' : '' ?>"
                                       placeholder="info@miempresa.com" required>
                            </div>
                            <?php if (isset($errors['company_email'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['company_email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" id="company_phone" name="company_phone" 
                                       value="<?= htmlspecialchars($old['company_phone'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="+34 912 345 678">
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                Dirección
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" id="address" name="address" 
                                       value="<?= htmlspecialchars($old['address'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Calle Principal 123">
                            </div>
                        </div>

                        <!-- Ciudad -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                Ciudad
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-city text-gray-400"></i>
                                </div>
                                <input type="text" id="city" name="city" 
                                       value="<?= htmlspecialchars($old['city'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Madrid">
                            </div>
                        </div>

                        <!-- Código Postal -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Código Postal
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-mail-bulk text-gray-400"></i>
                                </div>
                                <input type="text" id="postal_code" name="postal_code" 
                                       value="<?= htmlspecialchars($old['postal_code'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="28001">
                            </div>
                        </div>

                        <!-- País -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                                País
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                                <select id="country" name="country" 
                                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="ES" <?= ($old['country'] ?? 'ES') === 'ES' ? 'selected' : '' ?>>España</option>
                                    <option value="AR" <?= ($old['country'] ?? '') === 'AR' ? 'selected' : '' ?>>Argentina</option>
                                    <option value="MX" <?= ($old['country'] ?? '') === 'MX' ? 'selected' : '' ?>>México</option>
                                    <option value="CO" <?= ($old['country'] ?? '') === 'CO' ? 'selected' : '' ?>>Colombia</option>
                                    <option value="CL" <?= ($old['country'] ?? '') === 'CL' ? 'selected' : '' ?>>Chile</option>
                                    <option value="PE" <?= ($old['country'] ?? '') === 'PE' ? 'selected' : '' ?>>Perú</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="step = 1" 
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i> Anterior
                        </button>
                        <button type="button" @click="step = 3" 
                                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-semibold">
                            Continuar <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Datos del Administrador -->
                <div x-show="step === 3" x-transition>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 text-center">Datos del Administrador</h2>
                    <p class="text-gray-600 text-center mb-6">Esta persona será el superadministrador de la empresa</p>
                    
                    <div class="grid md:grid-cols-2 gap-5">
                        <!-- Nombre completo -->
                        <div class="md:col-span-2">
                            <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre completo <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="admin_name" name="admin_name" 
                                       value="<?= htmlspecialchars($old['admin_name'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['admin_name']) ? 'border-red-500' : '' ?>"
                                       placeholder="Juan García López" required>
                            </div>
                            <?php if (isset($errors['admin_name'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['admin_name'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Email del admin -->
                        <div class="md:col-span-2">
                            <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email del administrador <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="admin_email" name="admin_email" 
                                       value="<?= htmlspecialchars($old['admin_email'] ?? '') ?>"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['admin_email']) ? 'border-red-500' : '' ?>"
                                       placeholder="admin@miempresa.com" required>
                            </div>
                            <?php if (isset($errors['admin_email'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['admin_email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="admin_password" name="admin_password" 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['admin_password']) ? 'border-red-500' : '' ?>"
                                       placeholder="Mínimo 8 caracteres" required minlength="8">
                            </div>
                            <?php if (isset($errors['admin_password'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['admin_password'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div>
                            <label for="admin_password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="admin_password_confirm" name="admin_password_confirm" 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 <?= isset($errors['admin_password_confirm']) ? 'border-red-500' : '' ?>"
                                       placeholder="Repite la contraseña" required>
                            </div>
                            <?php if (isset($errors['admin_password_confirm'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['admin_password_confirm'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="mt-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="accept_terms" required
                                   class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600">
                                Acepto los <a href="<?= $baseUrl ?>terminos" class="text-primary-600 hover:underline" target="_blank">Términos de Servicio</a> 
                                y la <a href="<?= $baseUrl ?>privacidad" class="text-primary-600 hover:underline" target="_blank">Política de Privacidad</a>
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="step = 2" 
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i> Anterior
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            <i class="fas fa-check mr-2"></i> Crear Empresa
                        </button>
                    </div>
                </div>
            </form>

            <!-- Link a login -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    ¿Ya tienes una cuenta? 
                    <a href="<?= $baseUrl ?>login" class="text-primary-600 hover:underline font-semibold">Inicia sesión</a>
                </p>
            </div>
        </div>

        <!-- Garantías -->
        <div class="mt-8 flex flex-wrap justify-center gap-6 text-white/80 text-sm">
            <div class="flex items-center">
                <i class="fas fa-shield-alt mr-2"></i>
                Datos seguros SSL
            </div>
            <div class="flex items-center">
                <i class="fas fa-undo mr-2"></i>
                14 días de prueba gratis
            </div>
            <div class="flex items-center">
                <i class="fas fa-credit-card mr-2"></i>
                Sin tarjeta requerida
            </div>
        </div>
    </div>
</body>
</html>
