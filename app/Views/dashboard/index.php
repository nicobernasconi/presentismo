<?php
/**
 * Vista del Dashboard Principal
 * Variables disponibles:
 * - $stats: array con estadísticas generales
 * - $todayHours: horas trabajadas hoy
 * - $clockStatus: estado actual del fichaje
 * - $recentEntries: últimos fichajes
 * - $pendingApprovals: fichajes pendientes de aprobación (solo supervisores/admin)
 */
?>

<!-- Estadísticas principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Empleados -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1"><?= __('dashboard.total_employees') ?></p>
                <p class="text-3xl font-bold text-gray-900"><?= $stats['total_employees'] ?? 0 ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Presentes Hoy -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1"><?= __('dashboard.present_today') ?></p>
                <p class="text-3xl font-bold text-green-600"><?= $stats['present_today'] ?? 0 ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Ausentes Hoy -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1"><?= __('dashboard.absent_today') ?></p>
                <p class="text-3xl font-bold text-red-600"><?= $stats['absent_today'] ?? 0 ?></p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Horas Hoy -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1"><?= __('dashboard.working_hours') ?></p>
                <p class="text-3xl font-bold text-purple-600"><?= number_format($todayHours ?? 0, 2) ?>h</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-clock text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Widget de Fichaje -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-fingerprint text-primary-600 mr-2"></i>
                <?= __('dashboard.time_tracking') ?>
            </h3>

            <div class="text-center mb-6">
                <div class="text-5xl font-bold text-gray-900 mb-2">
                    <span id="current-time"><?= date('H:i:s') ?></span>
                </div>
                <div class="text-sm text-gray-600">
                    <?= date('l, d \d\e F \d\e Y') ?>
                </div>
            </div>

            <?php if ($clockStatus['is_clocked_in'] ?? false): ?>
                <!-- Estado: Fichado -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-green-800 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-semibold"><?= __('dashboard.checked_in') ?>:</span>
                    </div>
                    <div class="text-2xl font-bold text-green-900">
                        <?= date('H:i', strtotime($clockStatus['clock_in_time'] ?? '')) ?>
                    </div>
                    <div class="text-sm text-green-700 mt-2">
                        <?= __('time_entries.duration') ?>: <span id="elapsed-time" class="font-semibold"><?= $clockStatus['elapsed_time'] ?? '0:00' ?></span>
                    </div>
                </div>

                <form action="<?= $baseUrl ?>fichajes/clock-out" method="POST" data-confirm="<?= __('time_entries.confirm_check_out') ?? '¿Confirmas que deseas fichar la salida?' ?>">
                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <?= __('dashboard.check_out') ?>
                    </button>
                </form>
            <?php else: ?>
                <!-- Estado: Sin fichar -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-semibold"><?= __('time_entries.not_checked_in') ?></span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <?= __('time_entries.check_in_message') ?? 'Pulsa el botón para registrar tu entrada' ?>
                    </div>
                </div>

                <form action="<?= $baseUrl ?>fichajes/clock-in" method="POST" onsubmit="return getGeolocation(this)">
                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <?= __('dashboard.check_in') ?>
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="<?= $baseUrl ?>fichajes/historial" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    <?= __('time_entries.view_history') ?? 'Ver historial completo' ?> →
                </a>
            </div>
        </div>
    </div>

    <!-- Fichajes Recientes -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-history text-primary-600 mr-2"></i>
                <?= __('dashboard.recent_activity') ?>
            </h3>

            <?php if (!empty($recentEntries)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <?= __('common.date') ?>
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <?= __('time_entries.entry') ?>
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <?= __('time_entries.exit') ?>
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <?= __('time_entries.hours') ?>
                                </th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <?= __('common.status') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentEntries as $entry): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($entry['clock_in_time'])) ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <?= date('H:i', strtotime($entry['clock_in_time'])) ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <?= $entry['clock_out_time'] ? date('H:i', strtotime($entry['clock_out_time'])) : '-' ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">
                                        <?= $entry['total_hours'] ? number_format($entry['total_hours'], 2) . 'h' : '-' ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <?php
                                        $statusColors = [
                                            'approved' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'rejected' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabels = [
                                            'approved' => __('common.approved'),
                                            'pending' => __('common.pending'),
                                            'rejected' => __('common.rejected')
                                        ];
                                        $status = $entry['status'] ?? 'pending';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= $statusLabels[$status] ?? $status ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p><?= __('time_entries.no_entries') ?? 'No hay fichajes recientes' ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($pendingApprovals) && ($isSupervisor || $isAdmin)): ?>
            <!-- Fichajes Pendientes de Aprobación (Solo Supervisores/Admin) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-check-double text-orange-600 mr-2"></i>
                    <?= __('time_entries.pending_approvals') ?? 'Pendientes de Aprobación' ?>
                    <span class="ml-2 bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded-full">
                        <?= count($pendingApprovals) ?>
                    </span>
                </h3>

                <div class="space-y-3">
                    <?php foreach (array_slice($pendingApprovals, 0, 5) as $pending): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($pending['employee_name'] ?? 'N/A') ?></p>
                                <p class="text-sm text-gray-600">
                                    <?= date('d/m/Y H:i', strtotime($pending['clock_in_time'])) ?>
                                    <?php if ($pending['clock_out_time']): ?>
                                        - <?= date('H:i', strtotime($pending['clock_out_time'])) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="<?= $baseUrl ?>fichajes/<?= $pending['id'] ?>/aprobar" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit" class="text-green-600 hover:text-green-700 p-2" title="<?= __('common.approve') ?? 'Aprobar' ?>">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </button>
                                </form>
                                <a href="<?= $baseUrl ?>fichajes" class="text-blue-600 hover:text-blue-700 p-2" title="<?= __('common.view_details') ?? 'Ver detalles' ?>">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($pendingApprovals) > 5): ?>
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <a href="<?= $baseUrl ?>fichajes" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            <?= __('time_entries.view_all_pending') ?? 'Ver todos los pendientes' ?> (<?= count($pendingApprovals) - 5 ?> <?= __('common.more') ?? 'más' ?>) →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Actualizar reloj en tiempo real
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const clockElement = document.getElementById('current-time');
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Actualizar tiempo transcurrido si está fichado
function updateElapsedTime() {
    const elapsedElement = document.getElementById('elapsed-time');
    if (elapsedElement && <?= ($clockStatus['is_clocked_in'] ?? false) ? 'true' : 'false' ?>) {
        const clockInTime = new Date('<?= $clockStatus['clock_in_time'] ?? '' ?>');
        const now = new Date();
        const diff = Math.floor((now - clockInTime) / 1000); // segundos
        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        elapsedElement.textContent = `${hours}:${minutes.toString().padStart(2, '0')}`;
    }
}

// Geolocalización para fichajes
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
    } else {
        return true;
    }
}

// Iniciar actualizaciones
setInterval(updateClock, 1000);
setInterval(updateElapsedTime, 60000); // Actualizar cada minuto
updateClock();
updateElapsedTime();
</script>
