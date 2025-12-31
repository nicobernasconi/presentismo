<?php
/**
 * Vista: Administración de QR para fichaje
 * Variables:
 * - $workCenters: centros de trabajo
 * - $tenant: datos del tenant
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Fichaje por Código QR</h2>
    <p class="text-gray-600 mt-1">Genera códigos QR para que tus empleados puedan fichar fácilmente</p>
</div>

<!-- Información -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-blue-900">¿Cómo funciona?</h3>
            <ul class="mt-2 text-blue-800 space-y-1">
                <li><i class="fas fa-check mr-2"></i>Genera un código QR para tu empresa o centro de trabajo</li>
                <li><i class="fas fa-check mr-2"></i>Imprime o muestra el QR en la entrada del centro</li>
                <li><i class="fas fa-check mr-2"></i>Los empleados escanean el QR con su móvil</li>
                <li><i class="fas fa-check mr-2"></i>Se identifican con email y PIN (últimos 4 dígitos del DNI)</li>
                <li><i class="fas fa-check mr-2"></i>Registran entrada o salida con un solo toque</li>
            </ul>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- QR General de la Empresa -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-building text-primary-600 mr-2"></i>
            QR General de la Empresa
        </h3>
        <p class="text-gray-600 mb-4">
            Este código QR permite fichar a cualquier empleado de la empresa, sin importar su centro de trabajo.
        </p>
        <a href="<?= $baseUrl ?>/qr/generar" class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 text-center">
            <i class="fas fa-qrcode mr-2"></i>
            Generar QR General
        </a>
    </div>

    <!-- QR por Centro de Trabajo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-map-marker-alt text-primary-600 mr-2"></i>
            QR por Centro de Trabajo
        </h3>
        <p class="text-gray-600 mb-4">
            Genera un QR específico para cada centro. Valida la geolocalización del empleado.
        </p>
        <select id="work-center-select" class="w-full mb-4 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            <option value="">Selecciona un centro de trabajo</option>
            <?php foreach ($workCenters as $center): ?>
                <option value="<?= $center->id ?>"><?= htmlspecialchars($center->name) ?></option>
            <?php endforeach; ?>
        </select>
        <button onclick="generateCenterQR()" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
            <i class="fas fa-qrcode mr-2"></i>
            Generar QR del Centro
        </button>
    </div>
</div>

<script>
function generateCenterQR() {
    const select = document.getElementById('work-center-select');
    const centerId = select.value;
    
    if (!centerId) {
        alert('Por favor, selecciona un centro de trabajo');
        return;
    }
    
    window.location.href = '<?= $baseUrl ?>/qr/generar&work_center_id=' + centerId;
}
</script>

<!-- Historial de Fichajes por QR -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-history text-primary-600 mr-2"></i>
        Fichajes Recientes por QR
    </h3>
    <p class="text-gray-600">
        Los fichajes realizados mediante QR aparecerán en el historial de fichajes con la etiqueta "QR".
    </p>
    <a href="<?= $baseUrl ?>fichajes/historial" class="inline-block mt-4 text-primary-600 hover:text-primary-700 font-medium">
        Ver historial completo →
    </a>
</div>
