<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Presentismo - Control de Asistencia Empresarial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Presentismo</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#caracteristicas" class="text-gray-600 hover:text-primary-600 transition">Características</a>
                    <a href="#beneficios" class="text-gray-600 hover:text-primary-600 transition">Beneficios</a>
                    <a href="#precios" class="text-gray-600 hover:text-primary-600 transition">Precios</a>
                    <a href="public/index.php?route=login" class="px-4 py-2 text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition">
                        Iniciar Sesión
                    </a>
                    <a href="public/index.php?route=admin/login" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                        Panel Admin
                    </a>
                </div>
                <button class="md:hidden text-gray-600" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#caracteristicas" class="block text-gray-600 hover:text-primary-600">Características</a>
                <a href="#beneficios" class="block text-gray-600 hover:text-primary-600">Beneficios</a>
                <a href="#precios" class="block text-gray-600 hover:text-primary-600">Precios</a>
                <a href="public/index.php?route=login" class="block px-4 py-2 text-center text-primary-600 border border-primary-600 rounded-lg">
                    Iniciar Sesión
                </a>
                <a href="public/index.php?route=admin/login" class="block px-4 py-2 text-center bg-primary-600 text-white rounded-lg">
                    Panel Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Control de Asistencia
                        <span class="text-blue-200">Inteligente</span>
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                        Gestiona el fichaje de tus empleados de forma simple y eficiente. 
                        Automatiza el control de horarios, ausencias y vacaciones en una sola plataforma.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="public/index.php?route=register-company" 
                           class="px-8 py-4 bg-white text-primary-600 rounded-xl font-semibold text-lg hover:bg-blue-50 transition shadow-lg flex items-center justify-center">
                            <i class="fas fa-rocket mr-2"></i>
                            Registrar mi Empresa
                        </a>
                        <a href="#demo" 
                           class="px-8 py-4 border-2 border-white text-white rounded-xl font-semibold text-lg hover:bg-white/10 transition flex items-center justify-center">
                            <i class="fas fa-play-circle mr-2"></i>
                            Ver Demo
                        </a>
                    </div>
                    <div class="mt-10 flex items-center space-x-8">
                        <div class="flex -space-x-3">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-white" alt="Usuario">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-white" alt="Usuario">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-white" alt="Usuario">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-white" alt="Usuario">
                        </div>
                        <div class="text-blue-100">
                            <span class="font-bold text-white">+2,500</span> empresas confían en nosotros
                        </div>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="float-animation">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=400&fit=crop" 
                             alt="Dashboard de control" 
                             class="rounded-2xl shadow-2xl">
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Fichaje registrado</p>
                                <p class="text-sm text-gray-500">Hace 2 minutos</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-6 -right-6 bg-white p-4 rounded-xl shadow-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">48 empleados</p>
                                <p class="text-sm text-gray-500">Activos hoy</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logos Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 mb-8">Empresas que confían en Presentismo</p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60">
                <div class="text-2xl font-bold text-gray-400">TechCorp</div>
                <div class="text-2xl font-bold text-gray-400">Innovate</div>
                <div class="text-2xl font-bold text-gray-400">GlobalServ</div>
                <div class="text-2xl font-bold text-gray-400">DataFlow</div>
                <div class="text-2xl font-bold text-gray-400">CloudMax</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="caracteristicas" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold">CARACTERÍSTICAS</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Todo lo que necesitas para gestionar tu equipo</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                    Herramientas potentes y fáciles de usar para el control completo de la asistencia de tu empresa.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-fingerprint text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fichaje Digital</h3>
                    <p class="text-gray-600">
                        Registro de entrada y salida con un solo clic. Compatible con dispositivos móviles y geolocalización.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Gestión de Ausencias</h3>
                    <p class="text-gray-600">
                        Solicita y aprueba vacaciones, permisos y bajas médicas de forma automatizada.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Reportes Detallados</h3>
                    <p class="text-gray-600">
                        Genera informes de horas trabajadas, ausencias y productividad por empleado o departamento.
                    </p>
                </div>
                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Turnos Flexibles</h3>
                    <p class="text-gray-600">
                        Configura horarios rotativos, turnos nocturnos y jornadas personalizadas para cada empleado.
                    </p>
                </div>
                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Notificaciones</h3>
                    <p class="text-gray-600">
                        Alertas automáticas por retrasos, ausencias no justificadas y solicitudes pendientes.
                    </p>
                </div>
                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm card-hover">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-building text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Multi-empresa</h3>
                    <p class="text-gray-600">
                        Gestiona múltiples sucursales y centros de trabajo desde un panel centralizado.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="beneficios" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=500&fit=crop" 
                         alt="Equipo de trabajo" 
                         class="rounded-2xl shadow-xl">
                </div>
                <div>
                    <span class="text-primary-600 font-semibold">BENEFICIOS</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-8">
                        Ahorra tiempo y reduce errores administrativos
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Reducción del 80% en tareas administrativas</h4>
                                <p class="text-gray-600">Automatiza el registro de horas y generación de nóminas.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Cumplimiento legal garantizado</h4>
                                <p class="text-gray-600">Registro de jornada conforme a la normativa laboral vigente.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Visibilidad en tiempo real</h4>
                                <p class="text-gray-600">Sabe quién está trabajando en cada momento desde cualquier lugar.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Empleados más satisfechos</h4>
                                <p class="text-gray-600">Transparencia en el control de horas y gestión de permisos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-4xl md:text-5xl font-bold mb-2">2,500+</div>
                    <div class="text-blue-200">Empresas activas</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold mb-2">150K+</div>
                    <div class="text-blue-200">Empleados gestionados</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold mb-2">5M+</div>
                    <div class="text-blue-200">Fichajes registrados</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold mb-2">99.9%</div>
                    <div class="text-blue-200">Uptime garantizado</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="precios" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold">PRECIOS</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Planes para cada necesidad</h2>
                <p class="text-gray-600 mt-4">Sin permanencia. Cancela cuando quieras.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Plan Básico -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 card-hover">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Básico</h3>
                    <p class="text-gray-500 mb-6">Para pequeños equipos</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">€9</span>
                        <span class="text-gray-500">/mes</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Hasta 10 empleados
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Fichaje digital
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Reportes básicos
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Soporte email
                        </li>
                    </ul>
                    <a href="public/index.php?route=register-company" class="block w-full py-3 text-center border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition">
                        Empezar gratis
                    </a>
                </div>
                <!-- Plan Profesional -->
                <div class="bg-primary-600 p-8 rounded-2xl shadow-xl card-hover relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-yellow-400 text-yellow-900 px-4 py-1 rounded-full text-sm font-semibold">
                        Más popular
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Profesional</h3>
                    <p class="text-blue-200 mb-6">Para empresas en crecimiento</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-white">€29</span>
                        <span class="text-blue-200">/mes</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-white">
                            <i class="fas fa-check text-blue-200 mr-3"></i>
                            Hasta 50 empleados
                        </li>
                        <li class="flex items-center text-white">
                            <i class="fas fa-check text-blue-200 mr-3"></i>
                            Gestión de ausencias
                        </li>
                        <li class="flex items-center text-white">
                            <i class="fas fa-check text-blue-200 mr-3"></i>
                            Turnos y horarios
                        </li>
                        <li class="flex items-center text-white">
                            <i class="fas fa-check text-blue-200 mr-3"></i>
                            Reportes avanzados
                        </li>
                        <li class="flex items-center text-white">
                            <i class="fas fa-check text-blue-200 mr-3"></i>
                            Soporte prioritario
                        </li>
                    </ul>
                    <a href="public/index.php?route=register-company" class="block w-full py-3 text-center bg-white text-primary-600 rounded-lg hover:bg-blue-50 transition font-semibold">
                        Empezar gratis
                    </a>
                </div>
                <!-- Plan Enterprise -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 card-hover">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-gray-500 mb-6">Para grandes organizaciones</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">€79</span>
                        <span class="text-gray-500">/mes</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Empleados ilimitados
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Multi-empresa
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            API personalizada
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Integración nóminas
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Manager dedicado
                        </li>
                    </ul>
                    <a href="public/index.php?route=register-company" class="block w-full py-3 text-center border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition">
                        Contactar ventas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold">TESTIMONIOS</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Lo que dicen nuestros clientes</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 mb-6">
                        "Desde que implementamos Presentismo, ahorramos más de 10 horas semanales en gestión administrativa. La facilidad de uso es impresionante."
                    </p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=100&h=100&fit=crop&crop=face" 
                             class="w-12 h-12 rounded-full mr-4" alt="Cliente">
                        <div>
                            <p class="font-semibold text-gray-900">Carlos Rodríguez</p>
                            <p class="text-sm text-gray-500">Director RRHH, TechCorp</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 mb-6">
                        "La mejor inversión que hemos hecho. El control de fichajes en tiempo real nos ha permitido optimizar turnos y reducir el absentismo."
                    </p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=100&h=100&fit=crop&crop=face" 
                             class="w-12 h-12 rounded-full mr-4" alt="Cliente">
                        <div>
                            <p class="font-semibold text-gray-900">María García</p>
                            <p class="text-sm text-gray-500">CEO, Innovate Solutions</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 mb-6">
                        "El soporte técnico es excepcional. Cualquier duda la resuelven en minutos. Totalmente recomendado para empresas de cualquier tamaño."
                    </p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" 
                             class="w-12 h-12 rounded-full mr-4" alt="Cliente">
                        <div>
                            <p class="font-semibold text-gray-900">Javier Martínez</p>
                            <p class="text-sm text-gray-500">CTO, DataFlow Systems</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Empieza a controlar la asistencia de tu equipo hoy
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Prueba gratuita de 14 días. Sin tarjeta de crédito. Sin compromiso.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="public/index.php?route=register-company" 
                   class="px-8 py-4 bg-white text-primary-600 rounded-xl font-semibold text-lg hover:bg-blue-50 transition shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Crear cuenta gratis
                </a>
                <a href="public/index.php?route=login" 
                   class="px-8 py-4 border-2 border-white text-white rounded-xl font-semibold text-lg hover:bg-white/10 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Ya tengo cuenta
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Presentismo</span>
                    </div>
                    <p class="text-gray-400 mb-6">
                        La solución más completa para el control de asistencia y gestión de horarios de tu empresa.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-6">Producto</h4>
                    <ul class="space-y-3">
                        <li><a href="#caracteristicas" class="hover:text-white transition">Características</a></li>
                        <li><a href="#precios" class="hover:text-white transition">Precios</a></li>
                        <li><a href="#" class="hover:text-white transition">Integraciones</a></li>
                        <li><a href="#" class="hover:text-white transition">Actualizaciones</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-6">Empresa</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-white transition">Sobre nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Trabaja con nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="public/index.php?route=privacidad" class="hover:text-white transition">Política de privacidad</a></li>
                        <li><a href="public/index.php?route=terminos" class="hover:text-white transition">Términos de uso</a></li>
                        <li><a href="public/index.php?route=cookies" class="hover:text-white transition">Política de cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p>&copy; 2025 Presentismo. Todos los derechos reservados.</p>
                <div class="flex items-center space-x-6 mt-4 md:mt-0">
                    <a href="public/index.php?route=login" class="hover:text-white transition">
                        <i class="fas fa-building mr-2"></i>Portal Empresas
                    </a>
                    <a href="public/index.php?route=admin/login" class="hover:text-white transition">
                        <i class="fas fa-cog mr-2"></i>Panel Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Smooth scroll para los enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Cerrar menú móvil si está abierto
                    document.getElementById('mobileMenu').classList.add('hidden');
                }
            });
        });

        // Navbar con fondo al hacer scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-md');
            } else {
                nav.classList.remove('shadow-md');
            }
        });
    </script>
</body>
</html>
