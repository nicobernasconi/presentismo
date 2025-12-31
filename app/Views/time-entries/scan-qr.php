<?php
/**
 * Vista para fichar (empleados)
 * Opciones: Escanear QR o solo con ubicación
 */
?>

<div class="max-w-lg mx-auto">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Fichar</h2>
        <p class="text-gray-600 mt-1">Elige cómo deseas registrar tu fichaje</p>
    </div>

    <!-- Estado actual -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <?php if ($clockStatus['is_clocked_in'] ?? false): ?>
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                    <span class="text-green-700 font-medium">Fichado desde <?= date('H:i', strtotime($clockStatus['clock_in_time'] ?? '')) ?></span>
                <?php else: ?>
                    <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                    <span class="text-gray-600">Sin fichar</span>
                <?php endif; ?>
            </div>
            <span class="text-2xl font-bold text-gray-900" id="current-time"><?= date('H:i:s') ?></span>
        </div>
    </div>

    <!-- Selector de método -->
    <div id="method-selector" class="space-y-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 text-center mb-4">¿Cómo quieres fichar?</h3>
        
        <!-- Opción: Escanear QR -->
        <button onclick="showQrScanner()" class="w-full bg-white border-2 border-gray-200 hover:border-primary-500 rounded-xl p-6 text-left transition-all duration-200 group">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-primary-200 transition-colors">
                    <i class="fas fa-qrcode text-primary-600 text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Escanear código QR</h4>
                    <p class="text-gray-500 text-sm">Usa la cámara para escanear el QR de entrada o salida</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </div>
        </button>
        
        <!-- Opción: Solo ubicación -->
        <button onclick="showLocationMethod()" class="w-full bg-white border-2 border-gray-200 hover:border-blue-500 rounded-xl p-6 text-left transition-all duration-200 group">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-map-marker-alt text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Solo con ubicación</h4>
                    <p class="text-gray-500 text-sm">Ficha usando tu posición GPS actual</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </div>
        </button>
    </div>

    <!-- Sección: Escáner QR -->
    <div id="qr-scanner-section" class="hidden">
        <div class="flex items-center mb-4">
            <button onclick="showMethodSelector()" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800">Escanear código QR</h3>
        </div>
        
        <!-- Visor de cámara -->
        <div class="bg-black rounded-lg overflow-hidden mb-4 relative" style="aspect-ratio: 1;">
            <div id="qr-video" class="w-full h-full"></div>
            
            <!-- Overlay con marco de escaneo -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-64 h-64 border-4 border-white rounded-lg opacity-50"></div>
            </div>
            
            <!-- Mensaje de estado -->
            <div id="scan-status" class="absolute bottom-4 left-0 right-0 text-center">
                <span class="bg-black bg-opacity-70 text-white px-4 py-2 rounded-full text-sm">
                    <i class="fas fa-camera mr-2"></i>
                    Buscando código QR...
                </span>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
            <p class="text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Escanea el <strong>QR verde</strong> para entrada o el <strong>QR rojo</strong> para salida
            </p>
        </div>
    </div>

    <!-- Sección: Solo ubicación -->
    <div id="location-section" class="hidden">
        <div class="flex items-center mb-4">
            <button onclick="showMethodSelector()" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800">Fichar con ubicación</h3>
        </div>
        
        <!-- Estado de ubicación -->
        <div id="location-status" class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-location-arrow text-gray-400 text-2xl" id="location-icon"></i>
                </div>
                <p class="text-gray-600 mb-4" id="location-message">Obteniendo tu ubicación...</p>
                <div id="location-coords" class="text-xs text-gray-400 hidden"></div>
            </div>
        </div>
        
        <!-- Botones de fichaje -->
        <div id="clock-buttons" class="space-y-3 hidden">
            <?php if (!($clockStatus['is_clocked_in'] ?? false)): ?>
                <!-- Botón de entrada -->
                <button onclick="clockWithLocation('in')" id="btn-clock-in" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-3 text-xl"></i>
                    <span class="text-lg">Fichar ENTRADA</span>
                </button>
            <?php else: ?>
                <!-- Botón de salida -->
                <button onclick="clockWithLocation('out')" id="btn-clock-out" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-3 text-xl"></i>
                    <span class="text-lg">Fichar SALIDA</span>
                </button>
            <?php endif; ?>
        </div>
        
        <div id="location-error" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
            <p class="text-yellow-700 text-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <span id="location-error-message"></span>
            </p>
        </div>
    </div>

    <!-- Resultado del fichaje -->
    <div id="scan-result" class="hidden">
        <div id="result-success" class="hidden bg-green-50 border border-green-200 rounded-lg p-6 text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-green-800 mb-2" id="result-title">¡Fichaje registrado!</h3>
            <p class="text-green-700" id="result-message"></p>
            <p class="text-3xl font-bold text-green-900 mt-4" id="result-time"></p>
        </div>
        
        <div id="result-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-red-800 mb-2">Error</h3>
            <p class="text-red-700" id="error-message"></p>
        </div>
        
        <button onclick="resetAll()" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
            <i class="fas fa-redo mr-2"></i>
            Volver a fichar
        </button>
    </div>

    <!-- Botón alternativo -->
    <div class="mt-6 text-center">
        <a href="<?= $baseUrl ?>fichajes/historial" class="text-primary-600 hover:text-primary-700 font-medium">
            <i class="fas fa-list mr-1"></i>
            Ver mis fichajes
        </a>
    </div>
</div>

<!-- Librería QR Scanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let isScanning = true;
let currentLatitude = null;
let currentLongitude = null;
const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
const baseUrl = '<?= $baseUrl ?>';

// Actualizar reloj
setInterval(() => {
    document.getElementById('current-time').textContent = new Date().toLocaleTimeString('es-ES');
}, 1000);

// Mostrar selector de método
function showMethodSelector() {
    stopScanner();
    document.getElementById('method-selector').classList.remove('hidden');
    document.getElementById('qr-scanner-section').classList.add('hidden');
    document.getElementById('location-section').classList.add('hidden');
    document.getElementById('scan-result').classList.add('hidden');
}

// Mostrar escáner QR
function showQrScanner() {
    document.getElementById('method-selector').classList.add('hidden');
    document.getElementById('qr-scanner-section').classList.remove('hidden');
    document.getElementById('location-section').classList.add('hidden');
    startScanner();
}

// Mostrar método de ubicación
function showLocationMethod() {
    document.getElementById('method-selector').classList.add('hidden');
    document.getElementById('qr-scanner-section').classList.add('hidden');
    document.getElementById('location-section').classList.remove('hidden');
    getLocation();
}

// Obtener ubicación
function getLocation() {
    const statusDiv = document.getElementById('location-status');
    const icon = document.getElementById('location-icon');
    const message = document.getElementById('location-message');
    const coords = document.getElementById('location-coords');
    const buttons = document.getElementById('clock-buttons');
    const errorDiv = document.getElementById('location-error');
    
    icon.className = 'fas fa-spinner fa-spin text-blue-400 text-2xl';
    message.textContent = 'Obteniendo tu ubicación...';
    statusDiv.className = 'bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6';
    buttons.classList.add('hidden');
    errorDiv.classList.add('hidden');
    
    if (!navigator.geolocation) {
        showLocationError('Tu navegador no soporta geolocalización');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            currentLatitude = position.coords.latitude;
            currentLongitude = position.coords.longitude;
            
            icon.className = 'fas fa-check-circle text-green-500 text-2xl';
            message.textContent = '¡Ubicación obtenida correctamente!';
            statusDiv.className = 'bg-green-50 border border-green-200 rounded-lg p-6 mb-6';
            coords.textContent = `Lat: ${currentLatitude.toFixed(6)}, Lng: ${currentLongitude.toFixed(6)}`;
            coords.classList.remove('hidden');
            buttons.classList.remove('hidden');
        },
        (error) => {
            let errorMsg = 'No se pudo obtener tu ubicación';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg = 'Permiso de ubicación denegado. Por favor, activa el GPS.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg = 'Ubicación no disponible. Intenta en otro lugar.';
                    break;
                case error.TIMEOUT:
                    errorMsg = 'Tiempo de espera agotado. Intenta de nuevo.';
                    break;
            }
            showLocationError(errorMsg);
        },
        { 
            enableHighAccuracy: true, 
            timeout: 10000, 
            maximumAge: 0 
        }
    );
}

function showLocationError(msg) {
    const icon = document.getElementById('location-icon');
    const message = document.getElementById('location-message');
    const statusDiv = document.getElementById('location-status');
    const errorDiv = document.getElementById('location-error');
    const errorMsg = document.getElementById('location-error-message');
    
    icon.className = 'fas fa-exclamation-triangle text-yellow-500 text-2xl';
    message.textContent = 'No se pudo obtener ubicación';
    statusDiv.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6';
    
    errorMsg.textContent = msg;
    errorDiv.classList.remove('hidden');
    
    // Mostrar botones de todas formas (permitir fichar sin ubicación)
    document.getElementById('clock-buttons').classList.remove('hidden');
}

// Fichar con ubicación
function clockWithLocation(type) {
    const btn = document.getElementById(type === 'in' ? 'btn-clock-in' : 'btn-clock-out');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
    }
    
    const formData = new FormData();
    formData.append('action_type', type);
    formData.append('latitude', currentLatitude || '');
    formData.append('longitude', currentLongitude || '');
    formData.append('_token', csrfToken);
    
    fetch(baseUrl + 'fichajes/location-clock', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
    })
    .catch(error => {
        showResult({ success: false, message: 'Error de conexión. Intenta de nuevo.' });
    });
}

// Iniciar escáner QR
function startScanner() {
    isScanning = true;
    
    html5QrCode = new Html5Qrcode("qr-video");
    
    const config = { 
        fps: 10, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };
    
    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.error('Error al iniciar cámara:', err);
        document.getElementById('scan-status').innerHTML = `
            <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error: No se pudo acceder a la cámara
            </span>
        `;
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.log('Scanner ya detenido'));
        html5QrCode = null;
    }
}

function onScanSuccess(decodedText, decodedResult) {
    if (!isScanning) return;
    isScanning = false;
    
    stopScanner();
    
    document.getElementById('scan-status').innerHTML = `
        <span class="bg-yellow-500 text-white px-4 py-2 rounded-full text-sm">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Procesando...
        </span>
    `;
    
    processQrCode(decodedText);
}

function onScanFailure(error) {
    // Silencioso - continúa escaneando
}

function processQrCode(qrData) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                sendQrClockRequest(qrData, position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                sendQrClockRequest(qrData, null, null);
            },
            { timeout: 5000 }
        );
    } else {
        sendQrClockRequest(qrData, null, null);
    }
}

function sendQrClockRequest(qrData, latitude, longitude) {
    const formData = new FormData();
    formData.append('qr_data', qrData);
    formData.append('latitude', latitude || '');
    formData.append('longitude', longitude || '');
    formData.append('_token', csrfToken);
    
    fetch(baseUrl + 'fichajes/scan-qr', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
    })
    .catch(error => {
        showResult({ success: false, message: 'Error de conexión. Intenta de nuevo.' });
    });
}

function showResult(data) {
    document.getElementById('method-selector').classList.add('hidden');
    document.getElementById('qr-scanner-section').classList.add('hidden');
    document.getElementById('location-section').classList.add('hidden');
    document.getElementById('scan-result').classList.remove('hidden');
    
    if (data.success) {
        document.getElementById('result-success').classList.remove('hidden');
        document.getElementById('result-error').classList.add('hidden');
        
        const title = data.type === 'in' ? '¡Entrada registrada!' : '¡Salida registrada!';
        document.getElementById('result-title').textContent = title;
        document.getElementById('result-message').textContent = data.message;
        document.getElementById('result-time').textContent = data.time;
    } else {
        document.getElementById('result-success').classList.add('hidden');
        document.getElementById('result-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = data.message;
    }
}

function resetAll() {
    stopScanner();
    currentLatitude = null;
    currentLongitude = null;
    
    document.getElementById('result-success').classList.add('hidden');
    document.getElementById('result-error').classList.add('hidden');
    document.getElementById('scan-result').classList.add('hidden');
    
    // Recargar página para actualizar estado
    window.location.reload();
}
</script>
