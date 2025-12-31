<!DOCTYPE html>
<html lang="es">
<head>
    <?php
    // URLs para assets y rutas
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    
    // $assetsUrl siempre apunta directamente a la carpeta public
    if (!isset($assetsUrl)) {
        $assetsUrl = $scriptDir;
    }
    
    // $baseUrl para rutas (puede incluir index.php?route=)
    if (!isset($baseUrl)) {
        if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
            $baseUrl = $scriptDir . '/index.php?route=';
        } else {
            $baseUrl = '/presentismo/public';
        }
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php 
    // Obtener CSRF token de forma segura
    $csrfToken = '';
    if (class_exists('\Core\Session')) {
        try {
            $csrfToken = \Core\Session::csrf() ?? '';
        } catch (\Throwable $e) {
            // Silent fallback
        }
    }
    if (empty($csrfToken) && isset($_SESSION['csrf_token'])) {
        $csrfToken = $_SESSION['csrf_token'];
    }
    echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8');
    ?>">
    <title><?= $title ?? 'Sistema de Presentismo' ?></title>
    
    <!-- Tailwind CSS (cache busting) -->
    <?php $cssVersion = (defined('PUBLIC_PATH') && file_exists(PUBLIC_PATH . '/css/styles.css')) ? filemtime(PUBLIC_PATH . '/css/styles.css') : time(); ?>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css?v=<?= $cssVersion ?>">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Sidebar Styles */
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { transform: translateX(4px); }
        .sidebar-item.active { 
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
            border-left: 3px solid #6366f1;
        }
        
        /* Card Styles */
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15); }
        
        /* Stats Cards */
        .stat-card { position: relative; overflow: hidden; }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 100px; height: 100px;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        /* Form Inputs */
        .form-input-modern {
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
        }
        .form-input-modern:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        /* Buttons */
        .btn-primary-modern {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            transition: all 0.3s ease;
        }
        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.4);
        }
        
        /* Table Styles */
        .table-modern tbody tr { transition: all 0.2s ease; }
        .table-modern tbody tr:hover { background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%); }
        
        /* Animate on load */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.5s ease forwards; }
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        
        /* Glass effect */
        .glass { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php
    // Variables ya están disponibles del Controller
    // $baseUrl y $content ya fueron pasadas por Controller::view()
    $user = $user ?? [];
    
    // Funciones helper para verificar roles de forma segura
    // role_id: 1 = super_admin, 2 = admin, 3 = supervisor, 4 = employee
    $isSupervisor = function() use ($user) {
        $roleId = (int)($user['role_id'] ?? 0);
        // Solo super_admin (1), admin (2) y supervisor (3) son supervisores
        return $roleId >= 1 && $roleId <= 3;
    };
    
    $isAdmin = function() use ($user) {
        $roleId = (int)($user['role_id'] ?? 0);
        // Solo super_admin (1) y admin (2) son admins
        return $roleId >= 1 && $roleId <= 2;
    };
    
    // Funciones helper para mensajes de sesión
    $getSuccess = function() {
        if (function_exists('\\Core\\Session\\success')) {
            return \Core\Session::success() ?? null;
        }
        $msg = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        return $msg;
    };
    
    $getError = function() {
        if (function_exists('\\Core\\Session\\error')) {
            return \Core\Session::error() ?? null;
        }
        $msg = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        return $msg;
    };
    
    $getWarning = function() {
        $msg = $_SESSION['warning'] ?? null;
        unset($_SESSION['warning']);
        return $msg;
    };
    ?>
    
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"
             @click="sidebarOpen = false"
             x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed top-0 left-0 z-50 w-64 h-screen bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative lg:static lg:h-auto lg:w-64 shadow-sm">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-5 border-b border-gray-100">
                <a href="<?= $baseUrl ?>/dashboard" class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-800">Presentismo</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="p-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 64px);">
                
                <!-- Menú Principal -->
                <p class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Principal</p>
                
                <a href="<?= $baseUrl ?>/dashboard" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'bg-indigo-50 text-indigo-700 border-l-3 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-home w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('common.dashboard') ?>
                </a>
                
                <a href="<?= $baseUrl ?>/fichajes" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/fichajes') !== false && strpos($_SERVER['REQUEST_URI'], '/historial') === false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-fingerprint w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/fichajes') !== false && strpos($_SERVER['REQUEST_URI'], '/historial') === false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('time_entries.check_in') ?>
                </a>
                
                <a href="<?= $baseUrl ?>/fichajes/historial" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/historial') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-history w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/historial') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('time_entries.history') ?? 'Historial' ?>
                </a>
                
                <a href="<?= $baseUrl ?>/ausencias" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/ausencias') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-calendar-times w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/ausencias') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('absences.my_absences') ?>
                </a>

                <?php if ($isSupervisor()): ?>
                <!-- Gestión -->
                <p class="px-3 pt-5 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</p>
                
                <a href="<?= $baseUrl ?>/empleados" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/empleados') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-users w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/empleados') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('employees.employees') ?>
                </a>
                
                <a href="<?= $baseUrl ?>/departamentos" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/departamentos') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-sitemap w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/departamentos') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('employees.department') ?>s
                </a>
                
                <a href="<?= $baseUrl ?>/turnos" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/turnos') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-calendar-alt w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/turnos') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('common.shifts') ?? 'Turnos' ?>
                </a>
                
                <a href="<?= $baseUrl ?>/reportes" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-chart-bar w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('common.reports') ?? 'Reportes' ?>
                </a>
                <?php endif; ?>

                <?php if ($isAdmin()): ?>
                <!-- Administración -->
                <p class="px-3 pt-5 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                
                <a href="<?= $baseUrl ?>/qr" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/qr') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-qrcode w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/qr') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('common.qr_clock') ?? 'Fichaje QR' ?>
                </a>
                
                <a href="<?= $baseUrl ?>/centros" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/centros') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-building w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/centros') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('employees.work_center') ?>s
                </a>
                
                <a href="<?= $baseUrl ?>/configuracion" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/configuracion') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-cog w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/configuracion') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    <?= __('common.settings') ?>
                </a>
                <?php endif; ?>

                <!-- Usuario -->
                <div class="pt-5 mt-3 border-t border-gray-100">
                    <a href="<?= $baseUrl ?>/perfil" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/perfil') !== false ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i class="fas fa-user-circle w-5 text-center mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/perfil') !== false ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                        Mi Perfil
                    </a>
                    
                    <a href="<?= $baseUrl ?>/logout" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center mr-3"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 w-full bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
            <!-- Top navbar -->
            <header class="sticky top-0 z-30 glass border-b border-gray-200/50 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Page title + Breadcrumb -->
                    <div class="hidden lg:flex items-center space-x-3">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-home mr-2"></i>
                            <span>Inicio</span>
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span class="text-gray-900 font-medium"><?= $title ?? 'Dashboard' ?></span>
                        </div>
                    </div>

                    <!-- Mobile title -->
                    <h1 class="lg:hidden text-lg font-semibold text-gray-900"><?= $title ?? 'Dashboard' ?></h1>

                    <!-- Right side -->
                    <div class="flex items-center space-x-2">
                        <!-- Quick Actions -->
                        <button class="hidden sm:flex items-center space-x-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Nuevo</span>
                        </button>
                        
                        <!-- Notifications -->
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute top-1 right-1 w-4 h-4 bg-gradient-to-r from-red-500 to-pink-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold shadow-lg">3</span>
                            </button>
                            
                            <div x-show="notifOpen" @click.away="notifOpen = false" x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50" x-cloak>
                                <div class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                    <h4 class="font-semibold">Notificaciones</h4>
                                    <p class="text-xs text-indigo-200">Tienes 3 nuevas</p>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900">Nuevo empleado registrado</p>
                                            <p class="text-xs text-gray-500">Hace 5 minutos</p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-clock text-orange-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900">Solicitud de ausencia pendiente</p>
                                            <p class="text-xs text-gray-500">Hace 1 hora</p>
                                        </div>
                                    </a>
                                </div>
                                <a href="#" class="block px-4 py-2 text-center text-sm text-indigo-600 hover:bg-indigo-50 font-medium">
                                    Ver todas las notificaciones
                                </a>
                            </div>
                        </div>

                        <!-- User dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-3 hover:bg-gray-100 rounded-xl p-2 transition-colors">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-medium text-sm shadow-lg shadow-indigo-500/30">
                                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['name'] ?? 'Usuario') ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($user['role_name'] ?? 'Usuario') ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:block"></i>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 overflow-hidden"
                                 x-cloak>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($user['name'] ?? 'Usuario') ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                                </div>
                                <a href="<?= $baseUrl ?>/perfil" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user w-5 mr-3 text-gray-400"></i>
                                    Mi Perfil
                                </a>
                                <a href="<?= $baseUrl ?>/configuracion" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                    Configuración
                                </a>
                                
                                <!-- Idiomas -->
                                <div class="border-t border-gray-100 my-1"></div>
                                <div class="px-4 py-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Idioma</p>
                                    <div class="flex gap-2">
                                        <?php 
                                        $currentLang = locale();
                                        $langs = get_supported_languages();
                                        foreach ($langs as $code => $name):
                                        ?>
                                        <form method="POST" action="<?= $baseUrl ?>/cambiar-idioma" class="inline">
                                            <input type="hidden" name="language" value="<?= $code ?>">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <button type="submit" 
                                                    class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-all <?= $currentLang === $code ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                                <?= strtoupper(htmlspecialchars($code)) ?>
                                            </button>
                                        </form>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?= $baseUrl ?>/logout" class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <!-- Flash messages -->
                <?php $success = $getSuccess(); if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800"><?= htmlspecialchars($success) ?></span>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <?php $error = $getError(); if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-red-800"><?= htmlspecialchars($error) ?></span>
                    <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <?php $warning = $getWarning(); if ($warning): ?>
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-triangle text-amber-500 mr-3"></i>
                    <span class="text-amber-800"><?= htmlspecialchars($warning) ?></span>
                    <button @click="show = false" class="ml-auto text-amber-500 hover:text-amber-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Page header -->
                <div class="hidden lg:flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900"><?= $title ?? 'Dashboard' ?></h1>
                </div>

                <!-- Main content area -->
                <?= $content ?>
                <footer class="px-6 pb-6 text-sm text-gray-500 flex flex-wrap gap-3">
                    <a href="<?= $baseUrl ?>/privacidad" class="hover:text-primary-700">Privacidad</a>
                    <span aria-hidden="true">•</span>
                    <a href="<?= $baseUrl ?>/cookies" class="hover:text-primary-700">Cookies</a>
                    <span aria-hidden="true">•</span>
                    <a href="<?= $baseUrl ?>/terminos" class="hover:text-primary-700">Términos</a>
                </footer>
            </main>
        </div>
    </div>

        <!-- Global Dialogs (Alpine.js) -->
        <div x-data="dialog()" x-init="init()" x-cloak>
                <div x-show="open" class="fixed inset-0 bg-black/40 z-50" x-transition.opacity></div>
                <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                                </div>
                                <div class="px-6 py-5 text-gray-700">
                                        <p x-text="message"></p>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                                        <button x-show="type==='confirm'" @click="cancel()" type="button"
                                                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">Cancelar</button>
                                        <button @click="ok()" type="button"
                                                        class="px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white"
                                                        x-text="type==='alert' ? 'Entendido' : 'Confirmar'"></button>
                                </div>
                        </div>
                </div>
        </div>

        <script>
        function dialog() {
            return {
                open: false,
                type: 'alert',
                title: 'Confirmación',
                message: '',
                _resolve: null,
                init() {
                    window.Dialog = {
                        alert: (msg, title = 'Aviso') => this.show('alert', msg, title),
                        confirm: (msg, title = 'Confirmación') => this.show('confirm', msg, title)
                    };
                    document.addEventListener('submit', (e) => {
                        const form = e.target;
                        const msg = form?.dataset?.confirm;
                        if (msg) {
                            e.preventDefault();
                            window.Dialog.confirm(msg).then((ok) => { if (ok) form.submit(); });
                        }
                    }, true);
                    document.addEventListener('click', (e) => {
                        const el = e.target.closest('[data-confirm]');
                        if (el && el.tagName !== 'FORM') {
                            e.preventDefault();
                            const msg = el.getAttribute('data-confirm');
                            const href = el.getAttribute('href');
                            window.Dialog.confirm(msg).then((ok) => { if (ok && href) window.location.href = href; });
                        }
                    });
                },
                show(type, msg, title) {
                    this.type = type;
                    this.title = title;
                    this.message = msg;
                    this.open = true;
                    return new Promise((resolve) => { this._resolve = resolve; });
                },
                ok() { this.open = false; this._resolve?.(true); },
                cancel() { this.open = false; this._resolve?.(false); },
            }
        }
        </script>

    <script>
        // CSRF token para peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Helper para peticiones fetch
        async function api(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            };
            
            const response = await fetch(url, { ...defaultOptions, ...options });
            return response.json();
        }
    </script>
</body>
</html>
