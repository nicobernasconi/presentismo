<?php
/**
 * Vista: Mostrar QR generado (ENTRADA y SALIDA)
 * Variables:
 * - $qrUrl: URL codificada en el QR (para acceso público)
 * - $workCenter: centro de trabajo (opcional)
 * - $tenant: datos del tenant
 */

// QR para acceso público (sin login)
$qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrUrl);

// QR para escanear desde la app (formato: FICHAJE:token:tipo:workCenterId)
$token = $tenant['qr_token'];
$workCenterId = $workCenter ? $workCenter->id : '';
$qrEntrada = "FICHAJE:{$token}:in:{$workCenterId}";
$qrSalida = "FICHAJE:{$token}:out:{$workCenterId}";

$qrEntradaUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&color=16a34a&data=' . urlencode($qrEntrada);
$qrSalidaUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&color=dc2626&data=' . urlencode($qrSalida);
?>

<div class="mb-6">
    <a href="<?= $baseUrl ?>qr" class="text-primary-600 hover:text-primary-700 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>
        Volver
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            <?php if ($workCenter): ?>
                Códigos QR - <?= htmlspecialchars($workCenter->name) ?>
            <?php else: ?>
                Códigos QR - <?= htmlspecialchars($tenant['name']) ?>
            <?php endif; ?>
        </h2>
        <p class="text-gray-600">
            Imprime estos códigos QR y colócalos en lugares visibles para los empleados
        </p>
    </div>

    <!-- QR de ENTRADA y SALIDA para empleados logueados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- QR ENTRADA -->
        <div class="bg-white rounded-lg shadow-lg border-4 border-green-500 p-6 text-center">
            <div class="bg-green-100 text-green-800 font-bold text-xl py-2 rounded-lg mb-4">
                <i class="fas fa-sign-in-alt mr-2"></i>
                ENTRADA
            </div>
            <div class="bg-white p-4 rounded-lg inline-block mb-4">
                <img src="<?= $qrEntradaUrl ?>" 
                     alt="QR de Entrada" 
                     class="w-48 h-48 mx-auto">
            </div>
            <p class="text-gray-600 text-sm mb-4">
                Escanea para registrar tu <strong>llegada</strong>
            </p>
            <a href="<?= $qrEntradaUrl ?>&format=png" 
               download="qr-entrada-<?= $workCenter ? $workCenter->id : 'general' ?>.png"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition inline-block">
                <i class="fas fa-download mr-2"></i>
                Descargar
            </a>
        </div>

        <!-- QR SALIDA -->
        <div class="bg-white rounded-lg shadow-lg border-4 border-red-500 p-6 text-center">
            <div class="bg-red-100 text-red-800 font-bold text-xl py-2 rounded-lg mb-4">
                <i class="fas fa-sign-out-alt mr-2"></i>
                SALIDA
            </div>
            <div class="bg-white p-4 rounded-lg inline-block mb-4">
                <img src="<?= $qrSalidaUrl ?>" 
                     alt="QR de Salida" 
                     class="w-48 h-48 mx-auto">
            </div>
            <p class="text-gray-600 text-sm mb-4">
                Escanea para registrar tu <strong>salida</strong>
            </p>
            <a href="<?= $qrSalidaUrl ?>&format=png" 
               download="qr-salida-<?= $workCenter ? $workCenter->id : 'general' ?>.png"
               class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition inline-block">
                <i class="fas fa-download mr-2"></i>
                Descargar
            </a>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex justify-center gap-4 mb-8">
        <button onclick="printQR()" 
                class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition">
            <i class="fas fa-print mr-2"></i>
            Imprimir Ambos QR
        </button>
    </div>

    <!-- QR para acceso público (sin login) -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">
            <i class="fas fa-globe mr-2"></i>
            QR de Acceso Público (alternativo)
        </h3>
        <p class="text-gray-600 text-center mb-4">
            Para empleados sin la app. Requiere email y PIN.
        </p>
        <div class="text-center">
            <div class="bg-white p-4 rounded-lg inline-block border-2 border-gray-300">
                <img src="<?= $qrApiUrl ?>" 
                     alt="QR Público" 
                     class="w-40 h-40 mx-auto">
            </div>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            ¿Cómo usar estos códigos?
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-800">
            <div>
                <h4 class="font-semibold mb-2">QR de Entrada/Salida (Verde/Rojo):</h4>
                <ol class="list-decimal list-inside space-y-1 text-sm">
                    <li>El empleado abre la app y va a "Escanear QR"</li>
                    <li>Escanea el QR verde para entrada</li>
                    <li>Escanea el QR rojo para salida</li>
                    <li>¡Listo! El fichaje queda registrado</li>
                </ol>
            </div>
            <div>
                <h4 class="font-semibold mb-2">QR Público (alternativo):</h4>
                <ol class="list-decimal list-inside space-y-1 text-sm">
                    <li>Escanear con cámara del móvil</li>
                    <li>Introducir email registrado</li>
                    <li>Introducir PIN (4 dígitos del DNI)</li>
                    <li>Pulsar Entrada o Salida</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Versión para imprimir -->
<div id="print-area" class="hidden print:block">
    <div class="text-center p-8">
        <h1 class="text-3xl font-bold mb-2">
            <?php if ($workCenter): ?>
                <?= htmlspecialchars($workCenter->name) ?>
            <?php else: ?>
                <?= htmlspecialchars($tenant['name']) ?>
            <?php endif; ?>
        </h1>
        <p class="text-lg mb-8">Escanea el código correspondiente para fichar</p>
        
        <div style="display: flex; justify-content: space-around; align-items: flex-start;">
            <div style="text-align: center; padding: 20px;">
                <h2 style="font-size: 24px; color: #16a34a; margin-bottom: 10px;">✓ ENTRADA</h2>
                <img src="<?= $qrEntradaUrl ?>&size=250x250" alt="QR Entrada" style="width: 250px; height: 250px;">
            </div>
            <div style="text-align: center; padding: 20px;">
                <h2 style="font-size: 24px; color: #dc2626; margin-bottom: 10px;">✗ SALIDA</h2>
                <img src="<?= $qrSalidaUrl ?>&size=250x250" alt="QR Salida" style="width: 250px; height: 250px;">
            </div>
        </div>
    </div>
</div>

<script>
function copyUrl() {
    const input = document.getElementById('qr-url');
    input.select();
    document.execCommand('copy');
    alert('URL copiada al portapapeles');
}

function printQR() {
    window.print();
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
