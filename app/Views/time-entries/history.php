<?php
/**
 * Vista del Historial de Fichajes
 * Variables disponibles:
 * - $entries: listado de fichajes
 * - $filters: filtros aplicados
 * - $pagination: información de paginación
 * - $statistics: estadísticas del período
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Historial de Fichajes</h2>
    <p class="text-gray-600 mt-1">Consulta tu registro completo de asistencia</p>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= $baseUrl ?>fichajes/historial" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
            <input type="date" 
                   id="date_from" 
                   name="date_from" 
                   value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>"
                   class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>

        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
            <input type="date" 
                   id="date_to" 
                   name="date_to" 
                   value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>"
                   class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select id="status" 
                    name="status" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Todos</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Aprobado</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-search mr-2"></i>
                Buscar
            </button>
            <a href="<?= $baseUrl ?>fichajes/historial" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Estadísticas del Período -->
<?php if (!empty($statistics)): ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Horas</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($statistics['total_hours'] ?? 0, 2) ?>h</p>
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
                <p class="text-2xl font-bold text-gray-900"><?= $statistics['days_worked'] ?? 0 ?></p>
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
                <p class="text-2xl font-bold text-gray-900"><?= number_format($statistics['average_hours'] ?? 0, 2) ?>h</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Fichajes</p>
                <p class="text-2xl font-bold text-gray-900"><?= $statistics['total_entries'] ?? 0 ?></p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-list text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabla de Fichajes -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list text-primary-600 mr-2"></i>
                Registros
            </h3>
            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-file-excel mr-2"></i>
                Exportar Excel
            </button>
        </div>

        <?php if (!empty($entries)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
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
                                Estado
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notas
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($entries as $entry): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= date('d/m/Y', strtotime($entry['clock_in_time'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php
                                    $typeIcons = [
                                        'regular' => '<i class="fas fa-clock text-blue-600"></i> Regular',
                                        'overtime' => '<i class="fas fa-business-time text-orange-600"></i> Extra',
                                        'break' => '<i class="fas fa-coffee text-yellow-600"></i> Descanso'
                                    ];
                                    ?>
                                    <span class="flex items-center">
                                        <?= $typeIcons[$entry['type']] ?? $entry['type'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('H:i', strtotime($entry['clock_in_time'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $entry['clock_out_time'] ? date('H:i', strtotime($entry['clock_out_time'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <?= $entry['total_hours'] ? number_format($entry['total_hours'], 2) . 'h' : '-' ?>
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
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    <?= htmlspecialchars($entry['notes'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                    <div class="text-sm text-gray-700">
                        Mostrando <span class="font-medium"><?= $pagination['from'] ?></span> a 
                        <span class="font-medium"><?= $pagination['to'] ?></span> de 
                        <span class="font-medium"><?= $pagination['total'] ?></span> resultados
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <a href="?page=<?= $pagination['current_page'] - 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" 
                               class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Anterior
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <a href="?page=<?= $i ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" 
                               class="px-3 py-2 border rounded-lg text-sm font-medium <?= $i === $pagination['current_page'] ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?page=<?= $pagination['current_page'] + 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" 
                               class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Siguiente
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-inbox text-6xl"></i>
                </div>
                <p class="text-gray-600 text-lg">No se encontraron fichajes</p>
                <p class="text-gray-500 text-sm mt-2">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function exportToExcel() {
    window.location.href = '/fichajes/exportar?<?= http_build_query($filters ?? []) ?>';
}
</script>
