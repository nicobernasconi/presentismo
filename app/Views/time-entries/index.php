<?php
/**
 * Vista del Módulo de Fichajes
 * Variables disponibles:
 * - $todayEntries: fichajes de hoy
 * - $clockStatus: estado actual del fichaje
 * - $weekSummary: resumen semanal
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Fichajes de Hoy</h2>
    <p class="text-gray-600 mt-1">Gestiona tus entradas y salidas del día</p>
</div>

<!-- Widget de Fichaje Grande -->
<div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg shadow-lg p-8 mb-8 text-white">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Reloj -->
        <div class="text-center">
            <div class="text-6xl font-bold mb-2">
                <span id="current-time"><?= date('H:i:s') ?></span>
            </div>
            <div class="text-lg opacity-90">
                <?php
                if (class_exists('IntlDateFormatter')) {
                    $formatter = new IntlDateFormatter(
                        'es_ES',
                        IntlDateFormatter::FULL,
                        IntlDateFormatter::NONE,
                        date_default_timezone_get(),
                        IntlDateFormatter::GREGORIAN,
                        'EEEE, d \'de\' MMMM'
                    );
                    echo ucfirst($formatter->format(time()));
                } else {
                    // Fallback sin intl
                    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                    echo $dias[date('w')] . ', ' . date('d') . ' de ' . $meses[date('n')];
                }
                ?>
            </div>
        </div>

        <!-- Estado Actual -->
        <div class="flex items-center justify-center">
            <?php if ($clockStatus['is_clocked_in'] ?? false): ?>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-full w-24 h-24 flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-check-circle text-5xl"></i>
                    </div>
                    <p class="text-xl font-semibold mb-2">Fichado desde</p>
                    <p class="text-3xl font-bold"><?= date('H:i', strtotime($clockStatus['clock_in_time'] ?? '')) ?></p>
                    <p class="text-sm opacity-75 mt-2">
                        Tiempo: <span id="elapsed-time" class="font-semibold"><?= $clockStatus['elapsed_time'] ?? '0:00' ?></span>
                    </p>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-full w-24 h-24 flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-clock text-5xl"></i>
                    </div>
                    <p class="text-xl font-semibold">Sin fichar</p>
                    <p class="text-sm opacity-75 mt-2">Registra tu entrada</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Botón de Acción -->
        <div class="flex items-center justify-center">
            <?php if ($clockStatus['is_clocked_in'] ?? false): ?>
                <form action="<?= $baseUrl ?>fichajes/clock-out" method="POST" data-confirm="¿Confirmas que deseas fichar la salida?" class="w-full max-w-xs">
                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="w-full bg-white text-red-600 hover:bg-gray-100 font-bold py-4 px-6 rounded-lg transition duration-200 shadow-lg">
                        <i class="fas fa-sign-out-alt text-2xl block mb-2"></i>
                        Fichar Salida
                    </button>
                </form>
            <?php else: ?>
                <a href="https://seic.com.ar/presentismo/public/index.php?route=fichajes" class="w-full max-w-xs bg-white text-green-600 hover:bg-gray-100 font-bold py-4 px-6 rounded-lg transition duration-200 shadow-lg block text-center">
                    <i class="fas fa-sign-in-alt text-2xl block mb-2"></i>
                    Fichar Entrada
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Resumen Semanal -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Horas Esta Semana</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($weekSummary['total_hours'] ?? 0, 2) ?>h</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Días Trabajados</p>
                <p class="text-2xl font-bold text-gray-900"><?= $weekSummary['days_worked'] ?? 0 ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Promedio Diario</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($weekSummary['average_hours'] ?? 0, 2) ?>h</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Horas Extras</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($weekSummary['overtime_hours'] ?? 0, 2) ?>h</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-business-time text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Fichajes de Hoy -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-list text-primary-600 mr-2"></i>
            Fichajes de Hoy
        </h3>
        <a href="<?= $baseUrl ?>fichajes/historial" class="text-primary-600 hover:text-primary-700 font-medium">
            <i class="fas fa-history mr-1"></i>
            Ver Historial
        </a>
    </div>

    <?php if (!empty($todayEntries)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Entrada
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salida
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Horas
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ubicación
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notas
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($todayEntries as $entry): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php
                                $typeIcons = [
                                    'clock_in' => '<i class="fas fa-clock text-blue-600"></i> Regular',
                                    'regular' => '<i class="fas fa-clock text-blue-600"></i> Regular',
                                    'overtime' => '<i class="fas fa-business-time text-orange-600"></i> Extra',
                                    'break_start' => '<i class="fas fa-coffee text-yellow-600"></i> Descanso',
                                    'break_end' => '<i class="fas fa-coffee text-yellow-600"></i> Fin Descanso'
                                ];
                                ?>
                                <span class="flex items-center">
                                    <?= $typeIcons[$entry['type']] ?? '<i class="fas fa-clock text-blue-600"></i> ' . htmlspecialchars($entry['type'] ?? 'Regular') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= date('H:i:s', strtotime($entry['clock_in_time'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $entry['clock_out_time'] ? date('H:i:s', strtotime($entry['clock_out_time'])) : '<span class="text-green-600 font-semibold">En curso</span>' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                <?= $entry['total_hours'] ? number_format($entry['total_hours'], 2) . 'h' : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php if ($entry['clock_in_latitude'] && $entry['clock_in_longitude']): ?>
                                    <a href="https://www.google.com/maps?q=<?= $entry['clock_in_latitude'] ?>,<?= $entry['clock_in_longitude'] ?>" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-700"
                                       title="Ver en mapa">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php
                                $statusColors = [
                                    'approved' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'rejected' => 'bg-red-100 text-red-800'
                                ];
                                $statusLabels = [
                                    'approved' => 'Aprobado',
                                    'pending' => 'Pendiente',
                                    'rejected' => 'Rechazado'
                                ];
                                $status = $entry['status'] ?? 'pending';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= $statusLabels[$status] ?? $status ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= htmlspecialchars($entry['notes'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-inbox text-6xl"></i>
            </div>
            <p class="text-gray-600 text-lg">No hay fichajes registrados hoy</p>
            <p class="text-gray-500 text-sm mt-2">Pulsa el botón de arriba para registrar tu entrada</p>
        </div>
    <?php endif; ?>
</div>

<script>
// Actualizar reloj
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const clockElement = document.getElementById('current-time');
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Actualizar tiempo transcurrido
function updateElapsedTime() {
    const elapsedElement = document.getElementById('elapsed-time');
    if (elapsedElement && <?= ($clockStatus['is_clocked_in'] ?? false) ? 'true' : 'false' ?>) {
        const clockInTime = new Date('<?= $clockStatus['clock_in_time'] ?? '' ?>');
        const now = new Date();
        const diff = Math.floor((now - clockInTime) / 1000);
        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        elapsedElement.textContent = `${hours}:${minutes.toString().padStart(2, '0')}`;
    }
}

// Geolocalización
function getGeolocation(form) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                form.submit();
            },
            function(error) {
                if (window.Dialog && window.Dialog.alert) {
                    window.Dialog.alert('No se pudo obtener la ubicación. El fichaje se registrará sin geolocalización.');
                }
                form.submit();
            }
        );
        return false;
    }
    return true;
}

// Iniciar actualizaciones
setInterval(updateClock, 1000);
setInterval(updateElapsedTime, 60000);
updateClock();
updateElapsedTime();
</script>
