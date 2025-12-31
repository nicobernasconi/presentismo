<?php
/**
 * Vista: Página de fichaje por QR (pública)
 * Variables:
 * - $tenant: datos de la empresa
 * - $token: token del QR
 * - $workCenter: centro de trabajo (opcional)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
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
        .clock-display {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Mono', 'Droid Sans Mono', monospace;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo y empresa -->
        <div class="text-center text-white mb-6">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-clock text-primary-600 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($tenant['name']) ?></h1>
            <?php if ($workCenter): ?>
                <p class="text-primary-200 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?= htmlspecialchars($workCenter->name) ?>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Card principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Reloj -->
            <div class="bg-gray-900 text-white py-6 text-center">
                <div class="clock-display text-5xl font-bold mb-2" id="current-time">
                    <?= date('H:i:s') ?>
                </div>
                <div class="text-gray-400" id="current-date">
                    <?php
                    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                    echo $dias[date('w')] . ', ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
                    ?>
                </div>
            </div>
            
            <!-- Formulario -->
            <div class="p-6" id="form-container">
                <form id="clock-form" class="space-y-4">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="work_center_id" value="<?= htmlspecialchars($workCenterId ?? '') ?>">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               required
                               autocomplete="email"
                               placeholder="tu.email@empresa.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key text-gray-400 mr-1"></i>
                            PIN (últimos 4 dígitos del DNI)
                        </label>
                        <input type="password" 
                               name="pin" 
                               id="pin"
                               required
                               maxlength="4"
                               pattern="[0-9]{4}"
                               inputmode="numeric"
                               placeholder="••••"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg text-center tracking-widest">
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <button type="button" 
                                onclick="clock('in')"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl flex flex-col items-center">
                            <i class="fas fa-sign-in-alt text-2xl mb-1"></i>
                            <span>ENTRADA</span>
                        </button>
                        <button type="button" 
                                onclick="clock('out')"
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl flex flex-col items-center">
                            <i class="fas fa-sign-out-alt text-2xl mb-1"></i>
                            <span>SALIDA</span>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Resultado -->
            <div id="result-container" class="hidden p-6 text-center">
                <div id="result-icon" class="text-6xl mb-4"></div>
                <div id="result-message" class="text-xl font-semibold mb-2"></div>
                <div id="result-time" class="text-gray-600 mb-4"></div>
                <div id="result-user" class="text-gray-500 mb-6"></div>
                <button onclick="resetForm()" 
                        class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-8 rounded-xl transition">
                    <i class="fas fa-redo mr-2"></i>
                    Nuevo Fichaje
                </button>
            </div>
            
            <!-- Loading -->
            <div id="loading-container" class="hidden p-6 text-center">
                <div class="animate-spin text-primary-600 text-5xl mb-4">
                    <i class="fas fa-spinner"></i>
                </div>
                <p class="text-gray-600">Registrando fichaje...</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center text-primary-200 text-sm mt-6">
            <p>Sistema de Control de Presencia</p>
        </div>
    </div>
    
    <script>
        // Actualizar reloj
        function updateClock() {
            const now = new Date();
            document.getElementById('current-time').textContent = 
                now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000);
        
        // Obtener geolocalización
        function getGeolocation() {
            return new Promise((resolve) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById('latitude').value = position.coords.latitude;
                            document.getElementById('longitude').value = position.coords.longitude;
                            resolve(true);
                        },
                        () => resolve(false),
                        { enableHighAccuracy: true, timeout: 5000 }
                    );
                } else {
                    resolve(false);
                }
            });
        }
        
        // Función de fichaje
        async function clock(action) {
            const email = document.getElementById('email').value;
            const pin = document.getElementById('pin').value;
            
            if (!email || !pin) {
                alert('Por favor, introduce tu email y PIN');
                return;
            }
            
            if (pin.length !== 4) {
                alert('El PIN debe tener 4 dígitos');
                return;
            }
            
            // Mostrar loading
            document.getElementById('form-container').classList.add('hidden');
            document.getElementById('loading-container').classList.remove('hidden');
            
            // Obtener geolocalización
            await getGeolocation();
            
            // Preparar datos
            const formData = new FormData(document.getElementById('clock-form'));
            formData.append('action', action);
            
            try {
                const response = await fetch('<?= $baseUrl ?>fichar/accion', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Ocultar loading
                document.getElementById('loading-container').classList.add('hidden');
                
                // Mostrar resultado
                const resultContainer = document.getElementById('result-container');
                const resultIcon = document.getElementById('result-icon');
                const resultMessage = document.getElementById('result-message');
                const resultTime = document.getElementById('result-time');
                const resultUser = document.getElementById('result-user');
                
                if (data.success) {
                    resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    resultMessage.textContent = data.message;
                    resultMessage.className = 'text-xl font-semibold text-green-600 mb-2';
                    resultTime.textContent = 'Hora: ' + data.time;
                    resultUser.textContent = data.user || '';
                } else {
                    resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                    resultMessage.textContent = data.message;
                    resultMessage.className = 'text-xl font-semibold text-red-600 mb-2';
                    resultTime.textContent = '';
                    resultUser.textContent = '';
                }
                
                resultContainer.classList.remove('hidden');
                
            } catch (error) {
                document.getElementById('loading-container').classList.add('hidden');
                document.getElementById('form-container').classList.remove('hidden');
                alert('Error de conexión. Por favor, inténtalo de nuevo.');
            }
        }
        
        // Resetear formulario
        function resetForm() {
            document.getElementById('result-container').classList.add('hidden');
            document.getElementById('form-container').classList.remove('hidden');
            document.getElementById('email').value = '';
            document.getElementById('pin').value = '';
            document.getElementById('email').focus();
        }
        
        // Solicitar geolocalización al cargar
        getGeolocation();
    </script>
</body>
</html>
