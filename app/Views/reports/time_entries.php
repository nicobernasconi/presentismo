<?php
/**
 * Reportes - Fichajes
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Reporte de Fichajes</h2>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/fichajes" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg w-full">Filtrar</button>
        </div>
    </form>
</div>

<!-- Tabla de Reportes -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($timeEntries)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Salida</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Horas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($timeEntries as $entry): ?>
                    <?php 
                        $totalMinutes = $entry['total_minutes'] ?? 0;
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        $hoursFormatted = $totalMinutes > 0 ? sprintf('%d:%02d', $hours, $minutes) : '-';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($entry['name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y', strtotime($entry['clock_in_time'] ?? '')) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('H:i', strtotime($entry['clock_in_time'] ?? '')) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= $entry['clock_out_time'] ? date('H:i', strtotime($entry['clock_out_time'])) : '<span class="text-amber-600">En curso</span>' ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $hoursFormatted ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-gray-600">No hay datos para mostrar</p>
        <?php endif; ?>
    </div>
</div>
